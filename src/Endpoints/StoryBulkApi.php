<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Storyblok\ManagementApi\Data\StoriesData;
use Storyblok\ManagementApi\Data\StoryblokDataInterface;
use Storyblok\ManagementApi\Data\StoryData;
use Storyblok\ManagementApi\Exceptions\StoryblokApiException;
use Storyblok\ManagementApi\ManagementApiClient;
use Storyblok\ManagementApi\QueryParameters\Filters\QueryFilters;
use Storyblok\ManagementApi\QueryParameters\PaginationParams;
use Storyblok\ManagementApi\QueryParameters\StoriesParams;
use Storyblok\ManagementApi\Response\StoryblokResponseInterface;

/**
 * StoryApi handles all story-related operations in the Storyblok Management API
 *
 * This class provides methods to create, read, update and list stories
 * through the Storyblok Management API.
 */
class StoryBulkApi extends EndpointSpace
{
    private const int DEFAULT_ITEMS_PER_PAGE = 25;

    private const int DEFAULT_PAGE = 1;

    private const int RATE_LIMIT_STATUS_CODE = 429;

    private const int RETRY_DELAY = 1;

    private const int MAX_RETRIES = 3;

    private readonly StoryApi $api;

    /**
     * StoryApi constructor.
     */
    public function __construct(
        ManagementApiClient $managementClient,
        string|int $spaceId,
        LoggerInterface $logger = new NullLogger(),
    ) {
        parent::__construct($managementClient, $spaceId, $logger);
        $this->api = new StoryApi($managementClient, $spaceId, $logger);
    }

    /**
     * Retrieves all stories using pagination
     *
     * @param int $itemsPerPage Number of items to retrieve per page
     * @return \Generator<StoryData>
     * @throws StoryblokApiException
     */
    public function all(?StoriesParams $params = null, ?QueryFilters $filters = null, int $itemsPerPage = self::DEFAULT_ITEMS_PER_PAGE): \Generator
    {

        $totalPages = null;
        $retryCount = 0;
        $page = new PaginationParams(self::DEFAULT_PAGE, $itemsPerPage);

        do {
            try {
                $response = $this->api->page(
                    params: $params,
                    queryFilters: $filters,
                    page: $page,
                );

                if ($response->isOk()) {
                    $totalPages = $this->handleSuccessfulResponse($response, $totalPages, $itemsPerPage);
                    yield from $this->getStoriesFromResponse($response);
                    $page->incrementPage();
                    $retryCount = 0;
                } else {
                    $this->handleErrorResponse($response, $retryCount);
                    ++$retryCount;
                }
            } catch (\Exception $e) {
                $this->logger->error('Error fetching stories', [
                    'error' => $e->getMessage(),
                    'page' => $page->page(),
                ]);
                throw new StoryblokApiException('Failed to fetch stories: ' . $e->getMessage(), 0, $e);
            }
        } while ($page->page() <= $totalPages);
    }

    /**
     * Creates multiple stories with rate limit handling and retries
     *
     * @param StoryData[] $stories Array of stories to create
     * @return \Generator<StoryblokDataInterface> Generated stories
     * @throws StoryblokApiException
     */
    public function createStories(array $stories): \Generator
    {
        $retryCount = 0;

        foreach ($stories as $storyData) {
            while (true) {
                try {
                    $response = $this->api->create($storyData);
                    yield $response->data();
                    $retryCount = 0;
                    break;
                } catch (StoryblokApiException $e) {
                    if ($e->getCode() === self::RATE_LIMIT_STATUS_CODE) {
                        if ($retryCount >= self::MAX_RETRIES) {
                            $this->logger->error('Max retries reached while creating story', [
                                'story_name' => $storyData->name(),
                            ]);
                            throw new StoryblokApiException(
                                'Rate limit exceeded maximum retries',
                                self::RATE_LIMIT_STATUS_CODE,
                            );
                        }

                        $this->logger->warning('Rate limit reached while creating story, retrying...', [
                            'retry_count' => $retryCount + 1,
                            'max_retries' => self::MAX_RETRIES,
                            'story_name' => $storyData->name(),
                        ]);

                        $this->handleRateLimit();
                        ++$retryCount;
                        continue;
                    }

                    throw $e;
                }
            }
        }
    }

    /**
     * Handles successful API response
     */
    private function handleSuccessfulResponse(
        StoryblokResponseInterface $response,
        ?int $totalPages,
        int $itemsPerPage,
    ): int {
        if ($totalPages === null) {
            $totalPages = (int) ceil($response->total() / $itemsPerPage);
            $this->logger->info('Total stories found: ' . $response->total());
        }

        return $totalPages;
    }

    /**
     * Handles error responses from the API
     *
     * @throws StoryblokApiException
     */
    private function handleErrorResponse(StoryblokResponseInterface $response, int $retryCount): void
    {
        if ($response->getResponseStatusCode() === self::RATE_LIMIT_STATUS_CODE) {
            if ($retryCount < self::MAX_RETRIES) {
                $this->handleRateLimit();
            } else {
                throw new StoryblokApiException('Rate limit exceeded maximum retries');
            }
        } else {
            $this->logger->error('API error', [
                'status' => $response->getResponseStatusCode(),
                'message' => $response->getErrorMessage(),
            ]);
            throw new StoryblokApiException($response->getErrorMessage());
        }
    }

    /**
     * Handles rate limiting by implementing a delay
     */
    protected function handleRateLimit(): void
    {
        $this->logger->warning('Rate limit reached, waiting before retry...');
        sleep(self::RETRY_DELAY);
    }

    /**
     * Extracts stories from the API response
     *
     * @return \Generator<int, StoryData>
     */
    private function getStoriesFromResponse(StoryblokResponseInterface $response): \Generator
    {
        /** @var StoriesData $stories */
        $stories = $response->data();
        foreach ($stories as $story) {
            /** @var StoryData $story */
            yield $story;
        }
    }
}
