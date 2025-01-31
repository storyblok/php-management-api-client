<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Storyblok\ManagementApi\QueryParameters\AssetsParams;
use Storyblok\ManagementApi\QueryParameters\PaginationParams;
use Storyblok\ManagementApi\QueryParameters\StoriesParams;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Storyblok\ManagementApi\Data\StoriesData;
use Storyblok\ManagementApi\Data\StoryData;
use Storyblok\ManagementApi\Exceptions\InvalidStoryDataException;
use Storyblok\ManagementApi\Exceptions\StoryblokApiException;
use Storyblok\ManagementApi\StoryblokResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * StoryApi handles all story-related operations in the Storyblok Management API
 *
 * This class provides methods to create, read, update and list stories
 * through the Storyblok Management API.
 */
class StoryApi extends EndpointSpace
{
    private const int DEFAULT_ITEMS_PER_PAGE = 25;

    private const int DEFAULT_PAGE = 1;

    private const int RATE_LIMIT_STATUS_CODE = 429;

    private const int RETRY_DELAY = 1;

    private const int MAX_RETRIES = 3;

    /**
     * StoryApi constructor.
     */
    public function __construct(
        HttpClientInterface $httpClient,
        string|int $spaceId,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct($httpClient, $spaceId);
    }

    /**
     * Retrieves all stories using pagination
     *
     * @param int $itemsPerPage Number of items to retrieve per page
     * @return \Generator<StoryData>
     * @throws StoryblokApiException
     */
    public function all(?StoriesParams $params = null, int $itemsPerPage = self::DEFAULT_ITEMS_PER_PAGE): \Generator
    {

        $totalPages = null;
        $retryCount = 0;
        $page = new PaginationParams(self::DEFAULT_PAGE, $itemsPerPage);

        do {
            try {
                $response = $this->page($params, $page);

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
     * Retrieves a specific page of stories
     */
    public function page(
        ?StoriesParams $params = null,
        ?PaginationParams $page = null,
    ): StoryblokResponseInterface {
        if (!$params instanceof StoriesParams) {
            $params = new StoriesParams();
        }

        if (!$page instanceof PaginationParams) {
            $page = new PaginationParams();
        }

        $this->validatePaginationParams($page);

        $options = [
            'query' => array_merge($params->toArray(), $page->toArray()),
        ];

        return $this->makeRequest(
            "GET",
            $this->buildStoriesEndpoint(),
            options: $options,
            dataClass: StoriesData::class,
        );
    }

    /**
     * Retrieves a specific story by ID
     *
     * @throws StoryblokApiException
     */
    public function get(string $storyId): StoryblokResponseInterface
    {
        $this->validateStoryId($storyId);

        return $this->makeRequest(
            "GET",
            $this->buildStoryEndpoint($storyId),
            dataClass: StoryData::class,
        );
    }

    /**
     * Creates a new story
     *
     * @throws InvalidStoryDataException
     */
    public function create(StoryData $storyData): StoryblokResponseInterface
    {
        $this->validateStoryData($storyData);

        if (!$storyData->hasKey("content")) {
            $storyData->setContent([
                "component" => $storyData->defaultContentType(),
            ]);
        }

        return $this->makeRequest(
            "POST",
            $this->buildStoriesEndpoint(),
            [
                "body" => json_encode(["story" => $storyData->toArray()]),
            ],
            dataClass: StoryData::class,
        );
    }

    /**
     * Updates an existing story
     *
     * @throws InvalidStoryDataException
     */
    public function update(string $storyId, StoryData $storyData): StoryblokResponseInterface
    {
        $this->validateStoryId($storyId);
        //$this->validateStoryData($storyData);

        return $this->makeRequest(
            "PUT",
            $this->buildStoryEndpoint($storyId),
            [
                "body" => json_encode(["story" => $storyData->toArray()]),
            ],
            dataClass: StoryData::class,
        );
    }

    public function publish(
        string $storyId,
        int|string|null $release_id = null,
        string|null $language = null,
    ): StoryblokResponseInterface {
        $this->validateStoryId($storyId);
        //$this->validateStoryData($storyData);

        $queryParams = [];
        if (null !== $release_id) {
            $queryParams["release_id"] = $release_id;
        }

        if (null !== $language) {
            $queryParams["lang"] = $language;
        }

        return $this->makeRequest(
            "GET",
            sprintf('%s/%s/publish', $this->buildStoriesEndpoint(), $storyId),
            [

                "query" => $queryParams,

            ],
            dataClass: StoryData::class,
        );
    }

    public function unpublish(
        string $storyId,
        string|null $language = null,
    ): StoryblokResponseInterface {
        $this->validateStoryId($storyId);
        //$this->validateStoryData($storyData);

        $queryParams = [];

        if (null !== $language) {
            $queryParams["lang"] = $language;
        }

        return $this->makeRequest(
            "GET",
            sprintf('%s/%s/unpublish', $this->buildStoriesEndpoint(), $storyId),
            [

                "query" => $queryParams,

            ],
            dataClass: StoryData::class,
        );
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
    private function handleRateLimit(): void
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

    /**
     * Validates pagination parameters
     *
     * @throws \InvalidArgumentException
     */
    private function validatePaginationParams(PaginationParams $page): void
    {
        if ($page->page() < 1) {
            throw new \InvalidArgumentException('Page number must be greater than 0');
        }

        if ($page->perPage() < 1) {
            throw new \InvalidArgumentException('Items per page must be greater than 0');
        }
    }

    /**
     * Validates story ID
     *
     * @throws \InvalidArgumentException
     */
    private function validateStoryId(string $storyId): void
    {
        if ($storyId === '' || $storyId === '0') {
            throw new \InvalidArgumentException('Story ID cannot be empty');
        }
    }

    /**
     * Validates story data
     *
     * @throws InvalidStoryDataException
     */
    private function validateStoryData(StoryData $storyData): void
    {
        if (!$storyData->isValid()) {
            throw new InvalidStoryDataException('Invalid story data provided');
        }
    }

    /**
     * Builds the base endpoint for stories
     */
    private function buildStoriesEndpoint(): string
    {
        return sprintf('/v1/spaces/%s/stories', $this->spaceId);
    }

    /**
     * Builds the endpoint for a specific story
     */
    private function buildStoryEndpoint(string $storyId): string
    {
        return sprintf('%s/%s', $this->buildStoriesEndpoint(), $storyId);
    }
}
