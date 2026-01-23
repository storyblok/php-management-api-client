<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Psr\Log\NullLogger;
use Storyblok\ManagementApi\Data\Story;
use Storyblok\ManagementApi\Endpoints\StoryBulkApi;
use Storyblok\ManagementApi\Exceptions\StoryblokApiException;
use Storyblok\ManagementApi\ManagementApiClient;
use Storyblok\ManagementApi\QueryParameters\StoriesParams;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Symfony\Component\HttpClient\Response\MockResponse;

// This is a mock class to eliminate the sleep from the rate limit handling
class TestStoryBulkApi extends StoryBulkApi
{
    #[\Override]
    protected function handleRateLimit(): void
    {
        // No sleep and no logs for testing
    }
}

final class StoryBulkApiTest extends TestCase
{
    public function testStoryApiWorksWithCustomLogger(): void
    {
        // Create a mock logger that tracks log calls
        $mockLogger = new class extends NullLogger {
            /** @var array<int, array{level: mixed, message: string|\Stringable, context: array<mixed>}> */
            public array $logs = [];

            public function log(
                $level,
                string|\Stringable $message,
                array $context = [],
            ): void {
                $this->logs[] = [
                    'level' => $level,
                    'message' => $message,
                    'context' => $context,
                ];
            }

            public function error(
                string|\Stringable $message,
                array $context = [],
            ): void {
                $this->log('error', $message, $context);
            }
        };

        // Create a response that will trigger error logging
        $responses = [
            new MockResponse(
                '',
                [
                    'http_code' => 500,
                    'response_headers' => ['Content-Type: application/json'],
                ],
            ),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $storyBulkApi = new TestStoryBulkApi($mapiClient, '222', $mockLogger);

        // Use the all() method which we know triggers logging
        try {
            iterator_to_array($storyBulkApi->all());
        } catch (\Exception) {
            // Expected exception
        }

        // Verify that logs were recorded
        $this->assertNotEmpty($mockLogger->logs);
        $this->assertSame('error', $mockLogger->logs[0]['level']);
        $this->assertSame('API error', $mockLogger->logs[0]['message']);
    }

    public function testListOfStoriesParams(): void
    {
        $responses = [
            $this->mockResponse('list-stories', 200, [
                'total' => 10,
                'per-page' => 2,
                'page' => 1,
            ]),
            $this->mockResponse('list-stories', 429, [
                'total' => 10,
                'per-page' => 2,
                'page' => 2,
            ]),
            $this->mockResponse('list-stories', 200, [
                'total' => 10,
                'per-page' => 2,
                'page' => 2,
            ]),
            $this->mockResponse('list-stories', 200, [
                'total' => 10,
                'per-page' => 2,
                'page' => 3,
            ]),
            $this->mockResponse('list-stories', 200, [
                'total' => 10,
                'per-page' => 2,
                'page' => 4,
            ]),
            $this->mockResponse('list-stories', 200, [
                'total' => 10,
                'per-page' => 2,
                'page' => 5,
            ]),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $storyBulkApi = new TestStoryBulkApi($mapiClient, '222');

        foreach (
            $storyBulkApi->all(
                params: new StoriesParams(withTag: 'aaa', search: 'something'),
                itemsPerPage: 2,
            ) as $story
        ) {
            $this->assertSame('My third post', $story->name());
        }
    }

    public function testListOfStoriesMaxRetryOnTheFirstPage(): void
    {
        $this->expectException(StoryblokApiException::class);
        $this->expectExceptionMessage('Rate limit exceeded maximum retries');

        $responses = [
            $this->mockResponse('list-stories-page-1', 429),
            $this->mockResponse('list-stories-page-1', 429),
            $this->mockResponse('list-stories-page-1', 429),
            $this->mockResponse('list-stories-page-1', 429),
            $this->mockResponse('list-stories-page-1', 429),
            $this->mockResponse('list-stories-page-1', 200, [
                'total' => 6,
                'per-page' => 2,
                'page' => 1,
            ]),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $storyBulkApi = new TestStoryBulkApi($mapiClient, '222');

        iterator_to_array(
            $storyBulkApi->all(
                params: new StoriesParams(withTag: 'aaa', search: 'something'),
                itemsPerPage: 2,
            ),
        );
    }

    public function testAllWithSomeSparse429(): void
    {
        $responses = [
            $this->mockResponse('list-stories-page-1', 429),
            $this->mockResponse('list-stories-page-1', 200, [
                'total' => 6,
                'per-page' => 2,
                'page' => 1,
            ]),
            $this->mockResponse('list-stories-page-2', 429),
            $this->mockResponse('list-stories-page-2', 429),
            $this->mockResponse('list-stories-page-2', 200, [
                'total' => 6,
                'per-page' => 2,
                'page' => 2,
            ]),
            $this->mockResponse('list-stories-page-3', 200, [
                'total' => 6,
                'per-page' => 2,
                'page' => 3,
            ]),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $storyBulkApi = new TestStoryBulkApi($mapiClient, '222');

        $i = 0;
        $expectedNames = [
            'My first post',
            'My second post',
            'My third post',
            'My fourth post',
            'My fifth post',
            'My sixth post',
        ];

        foreach (
            $storyBulkApi->all(
                params: new StoriesParams(withTag: 'aaa', search: 'something'),
                itemsPerPage: 2,
            ) as $story
        ) {
            $this->assertSame($expectedNames[$i], $story->name());
            ++$i;
        }

        $this->assertSame(6, $i);
    }

    public function testAllWithALotOf429OnSecondPage(): void
    {
        $this->expectException(StoryblokApiException::class);
        $this->expectExceptionMessage('Rate limit exceeded maximum retries');

        $responses = [
            $this->mockResponse('list-stories-page-1', 429),
            $this->mockResponse('list-stories-page-1', 200, [
                'total' => 6,
                'per-page' => 2,
                'page' => 1,
            ]),
            $this->mockResponse('list-stories-page-2', 429),
            $this->mockResponse('list-stories-page-2', 429),
            $this->mockResponse('list-stories-page-2', 429),
            $this->mockResponse('list-stories-page-2', 429),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $storyBulkApi = new TestStoryBulkApi($mapiClient, '222');

        iterator_to_array(
            $storyBulkApi->all(
                params: new StoriesParams(withTag: 'aaa', search: 'something'),
                itemsPerPage: 2,
            ),
        );
    }

    public function test429OnFirstPageWithNoResponseHeaders(): void
    {
        $responses = [
            $this->mockResponse('list-stories-page-1', 429),
            $this->mockResponse('list-stories-page-1', 200, [
                'total' => 6,
                'per-page' => 2,
                'page' => 1,
            ]),
            $this->mockResponse('list-stories-page-2', 200, [
                'total' => 6,
                'per-page' => 2,
                'page' => 2,
            ]),
            $this->mockResponse('list-stories-page-3', 200, [
                'total' => 6,
                'per-page' => 2,
                'page' => 3,
            ]),
            $this->mockResponse('list-stories', 429),
            $this->mockResponse('list-stories', 200, [
                'total' => 10,
                'per-page' => 2,
                'page' => 4,
            ]),
            $this->mockResponse('list-stories', 200, [
                'total' => 10,
                'per-page' => 2,
                'page' => 5,
            ]),
            $this->mockResponse('list-stories', 200, [
                'total' => 10,
                'per-page' => 2,
                'page' => 6,
            ]),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $storyBulkApi = new TestStoryBulkApi($mapiClient, '222');

        $i = 0;
        $expectedNames = [
            'My first post',
            'My second post',
            'My third post',
            'My fourth post',
            'My fifth post',
            'My sixth post',
        ];

        foreach (
            $storyBulkApi->all(
                params: new StoriesParams(withTag: 'aaa', search: 'something'),
                itemsPerPage: 2,
            ) as $story
        ) {
            $this->assertSame($expectedNames[$i], $story->name());
            ++$i;
        }

        $this->assertSame(6, $i);
    }

    public function testCreateBulkHandlesRateLimitingAndCreatesMultipleStories(): void
    {
        $mockLogger = new class extends NullLogger {
            /** @var array<int, array{level: mixed, message: string|\Stringable, context: array<mixed>}> */
            public array $logs = [];

            public function log(
                $level,
                string|\Stringable $message,
                array $context = [],
            ): void {
                $this->logs[] = [
                    'level' => $level,
                    'message' => $message,
                    'context' => $context,
                ];
            }

            public function warning(
                string|\Stringable $message,
                array $context = [],
            ): void {
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
                'uuid' => '1234-5678',
            ],
        ];

        $story2Data = [
            'story' => [
                'name' => 'Story 2',
                'slug' => 'story-2',
                'content' => ['component' => 'blog'],
                'created_at' => '2024-02-08 09:41:59.123',
                'published_at' => null,
                'id' => 2,
                'uuid' => '8765-4321',
            ],
        ];

        $responses = [
            // First story - Rate limit hit, then success
            $this->mockResponse('empty-story', 429, [
                'error' => 'Rate limit exceeded',
            ]),
            new JsonMockResponse($story1Data, ['http_code' => 201]),
            // Second story - Immediate success
            new JsonMockResponse($story2Data, ['http_code' => 201]),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);

        // Use TestStoryApi instead of regular StoryApi
        $storyBulkApi = new TestStoryBulkApi($mapiClient, '222', $mockLogger);

        // Create test stories
        $stories = [
            Story::make([
                'name' => 'Story 1',
                'slug' => 'story-1',
                'content' => ['component' => 'blog'],
            ]),
            Story::make([
                'name' => 'Story 2',
                'slug' => 'story-2',
                'content' => ['component' => 'blog'],
            ]),
        ];

        // Execute bulk creation
        $createdStories = iterator_to_array(
            $storyBulkApi->createStories($stories),
        );

        // Verify number of created stories
        $this->assertCount(2, $createdStories);

        // Verify rate limit warning was logged
        $hasRateLimitWarning = false;
        foreach ($mockLogger->logs as $log) {
            if (
                $log['level'] === 'warning' &&
                $log['message'] === 'Rate limit reached while creating story, retrying...'
            ) {
                $hasRateLimitWarning = true;
                break;
            }
        }

        $this->assertTrue($hasRateLimitWarning);

        // Verify created stories
        $this->assertSame('Story 1', $createdStories[0]->name());
        $this->assertSame('Story 2', $createdStories[1]->name());
        $this->assertSame('story-1', $createdStories[0]->slug());
        $this->assertSame('story-2', $createdStories[1]->slug());
    }

    public function testCreateBulkThrowsExceptionWhenMaxRetriesIsReached(): void
    {
        $mockLogger = new class extends NullLogger {
            /** @var array<int, array{level: mixed, message: string|\Stringable, context: array<mixed>}> */
            public array $logs = [];

            public function log(
                $level,
                string|\Stringable $message,
                array $context = [],
            ): void {
                $this->logs[] = [
                    'level' => $level,
                    'message' => $message,
                    'context' => $context,
                ];
            }

            public function warning(
                string|\Stringable $message,
                array $context = [],
            ): void {
                $this->log('warning', $message, $context);
            }

            public function error(
                string|\Stringable $message,
                array $context = [],
            ): void {
                $this->log('error', $message, $context);
            }
        };

        // Create responses that always return rate limit error (429)
        // We need MAX_RETRIES + 1 responses to trigger the exception
        $responses = array_fill(
            0,
            4,
            new JsonMockResponse(
                [
                    'error' => 'Rate limit exceeded',
                ],
                [
                    'http_code' => 429,
                ],
            ),
        );

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);

        // Use TestStoryApi instead of regular StoryApi
        $storyApi = new TestStoryBulkApi($mapiClient, '222', $mockLogger);

        // Create test story
        $stories = [
            Story::make([
                'name' => 'Story 1',
                'slug' => 'story-1',
                'content' => ['component' => 'blog'],
            ]),
        ];

        // Execute bulk creation and expect exception
        try {
            iterator_to_array($storyApi->createStories($stories));
            self::fail('Expected StoryblokApiException was not thrown');
        } catch (StoryblokApiException $storyblokApiException) {
            $this->assertSame('Rate limit exceeded maximum retries', $storyblokApiException->getMessage());
        }

        // Verify warning logs for each retry
        $warningCount = 0;
        $hasErrorLog = false;

        foreach ($mockLogger->logs as $log) {
            if (
                $log['level'] === 'warning' &&
                $log['message'] === 'Rate limit reached while creating story, retrying...'
            ) {
                ++$warningCount;
            }

            if (
                $log['level'] === 'error' &&
                $log['message'] === 'Max retries reached while creating story'
            ) {
                $hasErrorLog = true;
            }
        }

        // We should see MAX_RETRIES number of warning logs
        $this->assertSame(3, $warningCount);
        $this->assertTrue($hasErrorLog);

        // Verify the last log context contains story information
        $lastErrorLog = array_filter(
            $mockLogger->logs,
            fn(array $log): bool => $log['level'] === 'error',
        );
        $lastErrorLog = end($lastErrorLog);

        $this->assertArrayHasKey('story_name', $lastErrorLog['context']);
        $this->assertSame('Story 1', $lastErrorLog['context']['story_name']);
    }
}
