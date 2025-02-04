<?php

declare(strict_types=1);

use Storyblok\ManagementApi\ManagementApiClient;
use Storyblok\ManagementApi\Data\StoryData;
use Storyblok\ManagementApi\QueryParameters\Filters\Filter;
use Storyblok\ManagementApi\QueryParameters\Filters\QueryFilters;
use Storyblok\ManagementApi\QueryParameters\PaginationParams;
use Storyblok\ManagementApi\QueryParameters\StoriesParams;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Symfony\Component\HttpClient\MockHttpClient;
use Psr\Log\NullLogger;

// This is a mock class to eliminate the sleep from the rate limit handling
class TestStoryApi extends \Storyblok\ManagementApi\Endpoints\StoryApi
{
    #[\Override]
    protected function handleRateLimit(): void
    {
        // No sleep and no logs for testing
    }
}

test('Testing One Story, StoryData', function (): void {
    $responses = [
        \mockResponse("one-story", 200),
        \mockResponse("empty-story", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $storyApi = $mapiClient->storyApi("222");

    $storyblokResponse = $storyApi->get("111");
    /** @var \Storyblok\Mapi\Data\StoryData $storyblokData */
    $storyblokData =  $storyblokResponse->data();
    expect($storyblokData->get("name"))
        ->toBe("My third post")
        ->and($storyblokData->name())->toBe("My third post")
        ->and($storyblokData->createdAt())->toBe("2024-02-08")
        ->and($storyblokResponse->getResponseStatusCode())->toBe(200);

    $storyblokResponse = $storyApi->get("111notexists");
    expect( $storyblokResponse->getResponseStatusCode())->toBe(404) ;
    expect( $storyblokResponse->asJson())->toBe('["This record could not be found"]');
});

test('Create story encodes data correctly as JSON', function (): void {
    $expectedStoryData = [
        'name' => 'Test Story',
        'slug' => 'test-story',
        'content' => [
            'component' => 'blog-post',
            'title' => 'Test Title'
        ]
    ];

    // Create a callback to verify the request
    $callback = function ($method, $url, array $options) use ($expectedStoryData): void {
        expect($method)->toBe('POST');
        expect($url)->toContain('/stories');

        // Decode the request body and verify it matches expected structure
        $requestBody = json_decode((string) $options['body'], true);
        expect($requestBody)->toBeArray();
        expect($requestBody)->toHaveKey('story');
        expect($requestBody['story'])->toEqual($expectedStoryData);
    };

    // Create mock response
    $response = new MockResponse(json_encode([
        'story' => $expectedStoryData
    ]), [
        'http_code' => 201,
        'response_headers' => ['Content-Type: application/json'],
    ]);

    // Create mock client with response and callback
    $client = new MockHttpClient([$response], 'https://api.storyblok.com');
    $mapiClient = ManagementApiClient::initTest($client);
    $storyApi = $mapiClient->storyApi('222');

    // Create story data
    $storyData = StoryData::make($expectedStoryData);

    // Make the request
    $response = $storyApi->create($storyData);

    // Verify response
    expect($response->isOk())->toBeTrue();
    expect($response->getResponseStatusCode())->toBe(201);

    /** @var StoryData $responseData */
    $responseData = $response->data();
    expect($responseData->name())->toBe('Test Story');
    expect($responseData->slug())->toBe('test-story');
});

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
    $storyApi = $mapiClient->storyApi('222', $mockLogger);

    // Use the all() method which we know triggers logging
    try {
        iterator_to_array($storyApi->all());
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
    $storyApi = $mapiClient->storyApi("222");

    $storyblokResponse = $storyApi->page(params: new StoriesParams(
        favorite: true
    ));
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*favorite=1.*$/');
    expect($string)->toMatch('/.*page=1&per_page=25.*$/');

    $storyblokResponse = $storyApi->page(
        params: new StoriesParams(
            favorite: true
        ),page: new PaginationParams(5, 30)
    );
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*favorite=1.*$/');
    expect($string)->toMatch('/.*page=5&per_page=30.*$/');

    $storyblokResponse = $storyApi->page(
        params: new StoriesParams(
            withTag: "aaa",
            search: "something"
        ),page: new PaginationParams(5, 30)
    );
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*search=something.*$/');
    expect($string)->toMatch('/.*with_tag=aaa.*$/');
    expect($string)->toMatch('/.*page=5&per_page=30.*$/');


    $storyblokResponse = $storyApi->page(
        params: new StoriesParams(
            withTag: "aaa",
            search: "something"
        ),
        queryFilters: (new QueryFilters())->add(
            new Filter(
                "headline",
                "like",
                "something"
            )
        ),

        page: new PaginationParams(5, 30)
    );
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*search=something.*$/');
    expect($string)->toMatch('/.*with_tag=aaa.*$/');
    expect($string)->toMatch('/.*page=5&per_page=30.*$/');
    expect($string)->toMatch('/.*filter_query\[headline\]\[like\]=something.*$/');

    $storyblokResponse = $storyApi->page(
        params: new StoriesParams(
            withTag: "aaa",
            search: "something"
        ),
        queryFilters: (new QueryFilters())->add(
            new Filter(
                "headline",
                "like",
                "something"
            ))->add(
            new Filter(
                "subheadline",
                "like",
                "somethingelse"
            ),

        ),

        page: new PaginationParams(5, 30)
    );
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*search=something.*$/');
    expect($string)->toMatch('/.*with_tag=aaa.*$/');
    expect($string)->toMatch('/.*page=5&per_page=30.*$/');
    expect($string)->toMatch('/.*filter_query\[headline\]\[like\]=something.*$/');
    expect($string)->toMatch('/.*filter_query\[subheadline\]\[like\]=somethingelse.*$/');

    $storyblokResponse = $storyApi->all(
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
    $storyApi = new TestStoryApi($client, '222', $mockLogger);

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
    $createdStories = iterator_to_array($storyApi->createBulk($stories));

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
    $storyApi = new TestStoryApi($client, '222', $mockLogger);

    // Create test story
    $stories = [
        StoryData::make([
            'name' => 'Story 1',
            'slug' => 'story-1',
            'content' => ['component' => 'blog']
        ]),
    ];

    // Execute bulk creation and expect exception
    expect(fn (): array => iterator_to_array($storyApi->createBulk($stories)))
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
