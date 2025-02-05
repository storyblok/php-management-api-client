<?php

declare(strict_types=1);

use Storyblok\ManagementApi\ManagementApiClient;
use Storyblok\ManagementApi\Data\StoryData;
use Storyblok\ManagementApi\QueryParameters\StoriesParams;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Symfony\Component\HttpClient\MockHttpClient;
use Psr\Log\NullLogger;

// This is a mock class to eliminate the sleep from the rate limit handling
class TestStoryBulkApi extends \Storyblok\ManagementApi\Endpoints\StoryBulkApi
{
    #[\Override]
    protected function handleRateLimit(): void
    {
        // No sleep and no logs for testing
    }
}


test('StoryApi works with custom logger', function (): void {
    // Create a mock logger that tracks log calls
    $mockLogger = new class extends NullLogger {
        public array $logs = [];

        public function log($level, string|\Stringable $message, array $context = []): void
        {
            $this->logs[] = [
                'level' => $level,
                'message' => $message,
                'context' => $context
            ];
        }

        public function error(string|\Stringable $message, array $context = []): void
        {
            $this->log('error', $message, $context);
        }
    };

    // Create a response that will trigger error logging
    $responses = [
        new MockResponse([], [
            'http_code' => 500,
            'response_headers' => ['Content-Type: application/json'],
        ])
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $storyBulkApi = $mapiClient->storyBulkApi('222', $mockLogger);

    // Use the all() method which we know triggers logging
    try {
        iterator_to_array($storyBulkApi->all());
    } catch (\Exception) {
        // Expected exception
    }

    // Verify that logs were recorded
    expect($mockLogger->logs)->not->toBeEmpty()
        ->and($mockLogger->logs[0]['level'])->toBe('error')
        ->and($mockLogger->logs[0]['message'])->toBe('Error fetching stories');
});


test('Testing list of stories, Params', function (): void {
    $responses = [
        \mockResponse("list-stories", 200, ["total"=>2, "per-page" => 25 ]),
        \mockResponse("list-stories", 200, ["total"=>2, "per-page" => 25 ]),
        \mockResponse("list-stories", 200, ["total"=>2, "per-page" => 25 ]),
        \mockResponse("list-stories", 200, ["total"=>2, "per-page" => 25 ]),
        \mockResponse("list-stories", 200, ["total"=>2, "per-page" => 25 ]),
        \mockResponse("list-stories", 200, ["total"=>2, "per-page" => 25 ]),
        //\mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $storyBulkApi = $mapiClient->storyBulkApi("222");

    $storyblokResponse = $storyBulkApi->all(
        params: new StoriesParams(
            withTag: "aaa",
            search: "something"
        )
    );
    expect($storyblokResponse)->toBeInstanceOf(Generator::class);
    foreach ($storyblokResponse as $story) {
        expect($story->name())->toBe("My third post");
    }
});

test('createBulk handles rate limiting and creates multiple stories', function (): void {
    $mockLogger = new class extends NullLogger {
        public array $logs = [];

        public function log($level, string|\Stringable $message, array $context = []): void
        {
            $this->logs[] = [
                'level' => $level,
                'message' => $message,
                'context' => $context
            ];
        }

        public function warning(string|\Stringable $message, array $context = []): void
        {
            $this->log('warning', $message, $context);
        }
    };

    $story1Data = [
        'story' => [
            'name' => 'Story 1',
            'slug' => 'story-1',
            'content' => ['component' => 'blog'],
            'created_at' => '2024-02-08 09:40:59.123',
            'published_at' => null,
            'id' => 1,
            'uuid' => '1234-5678'
        ]
    ];

    $story2Data = [
        'story' => [
            'name' => 'Story 2',
            'slug' => 'story-2',
            'content' => ['component' => 'blog'],
            'created_at' => '2024-02-08 09:41:59.123',
            'published_at' => null,
            'id' => 2,
            'uuid' => '8765-4321'
        ]
    ];

    $responses = [
        // First story - Rate limit hit, then success
        \mockResponse('empty-story', 429, ['error' => 'Rate limit exceeded']),
        new JsonMockResponse($story1Data, ['http_code' => 201]),
        // Second story - Immediate success
        new JsonMockResponse($story2Data, ['http_code' => 201]),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);

    // Use TestStoryApi instead of regular StoryApi
    $storyBulkApi = new TestStoryBulkApi($client, '222', $mockLogger);

    // Create test stories
    $stories = [
        StoryData::make([
            'name' => 'Story 1',
            'slug' => 'story-1',
            'content' => ['component' => 'blog']
        ]),
        StoryData::make([
            'name' => 'Story 2',
            'slug' => 'story-2',
            'content' => ['component' => 'blog']
        ]),
    ];

    // Execute bulk creation
    $createdStories = iterator_to_array($storyBulkApi->createStories($stories));

    // Verify number of created stories
    expect($createdStories)->toHaveCount(2);

    // Verify rate limit warning was logged
    $hasRateLimitWarning = false;
    foreach ($mockLogger->logs as $log) {
        if ($log['level'] === 'warning' && $log['message'] === 'Rate limit reached while creating story, retrying...') {
            $hasRateLimitWarning = true;
            break;
        }
    }

    expect($hasRateLimitWarning)->toBeTrue();

    // Verify created stories
    expect($createdStories[0]->name())->toBe('Story 1');
    expect($createdStories[1]->name())->toBe('Story 2');
    expect($createdStories[0]->slug())->toBe('story-1');
    expect($createdStories[1]->slug())->toBe('story-2');
});

test('createBulk throws exception when max retries is reached', function (): void {
    $mockLogger = new class extends NullLogger {
        public array $logs = [];

        public function log($level, string|\Stringable $message, array $context = []): void
        {
            $this->logs[] = [
                'level' => $level,
                'message' => $message,
                'context' => $context
            ];
        }

        public function warning(string|\Stringable $message, array $context = []): void
        {
            $this->log('warning', $message, $context);
        }

        public function error(string|\Stringable $message, array $context = []): void
        {
            $this->log('error', $message, $context);
        }
    };

    // Create responses that always return rate limit error (429)
    // We need MAX_RETRIES + 1 responses to trigger the exception
    $responses = array_fill(0, 4, new JsonMockResponse([
        'error' => 'Rate limit exceeded'
    ], [
        'http_code' => 429
    ]));

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);

    // Use TestStoryApi instead of regular StoryApi
    $storyApi = new TestStoryBulkApi($client, '222', $mockLogger);

    // Create test story
    $stories = [
        StoryData::make([
            'name' => 'Story 1',
            'slug' => 'story-1',
            'content' => ['component' => 'blog']
        ]),
    ];

    // Execute bulk creation and expect exception
    expect(fn (): array => iterator_to_array($storyApi->createStories($stories)))
        ->toThrow(
            \Storyblok\ManagementApi\Exceptions\StoryblokApiException::class,
            'Rate limit exceeded maximum retries'
        );

    // Verify warning logs for each retry
    $warningCount = 0;
    $hasErrorLog = false;

    foreach ($mockLogger->logs as $log) {
        if ($log['level'] === 'warning' &&
            $log['message'] === 'Rate limit reached while creating story, retrying...'
        ) {
            ++$warningCount;
        }

        if ($log['level'] === 'error' &&
            $log['message'] === 'Max retries reached while creating story'
        ) {
            $hasErrorLog = true;
        }
    }

    // We should see MAX_RETRIES number of warning logs
    expect($warningCount)->toBe(3)
        ->and($hasErrorLog)->toBeTrue();

    // Verify the last log context contains story information
    $lastErrorLog = array_filter($mockLogger->logs, fn($log): bool => $log['level'] === 'error');
    $lastErrorLog = end($lastErrorLog);

    expect($lastErrorLog['context'])->toHaveKey('story_name')
        ->and($lastErrorLog['context']['story_name'])->toBe('Story 1');
});
