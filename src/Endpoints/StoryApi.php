<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Storyblok\ManagementApi\Data\StoriesData;
use Storyblok\ManagementApi\Data\StoryData;
use Storyblok\ManagementApi\Exceptions\InvalidStoryDataException;
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
class StoryApi extends EndpointSpace
{
    /**
     * StoryApi constructor.
     */
    public function __construct(
        ManagementApiClient $managementClient,
        string|int $spaceId,
        LoggerInterface $logger = new NullLogger(),
    ) {
        parent::__construct($managementClient, $spaceId, $logger);
    }

    /**
     * Retrieves a specific page of stories
     */
    public function page(
        ?StoriesParams $params = null,
        ?QueryFilters $queryFilters = null,
        ?PaginationParams $page = null,
    ): StoryblokResponseInterface {
        if (!$params instanceof StoriesParams) {
            $params = new StoriesParams();
        }

        if (!$queryFilters instanceof QueryFilters) {
            $queryFilters = new QueryFilters();
        }


        if (!$page instanceof PaginationParams) {
            $page = new PaginationParams();
        }

        $this->validatePaginationParams($page);

        $options = [
            'query' => array_merge(
                $params->toArray(),
                $queryFilters->toArray(),
                $page->toArray(),
            ),
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
     * @throws StoryblokApiException
     */
    public function create(StoryData $storyData): StoryblokResponseInterface
    {
        $this->validateStoryData($storyData);

        if (!$storyData->hasKey("content")) {
            $storyData->setContent([
                "component" => $storyData->defaultContentType(),
            ]);
        }

        try {
            $response = $this->makeRequest(
                "POST",
                $this->buildStoriesEndpoint(),
                [
                    "body" => json_encode(["story" => $storyData->toArray()]),
                ],
                dataClass: StoryData::class,
            );

            if ($response->isOk()) {
                $this->logger->info('Story created successfully', [
                    'story_name' => $storyData->name(),
                ]);
                return $response;
            }

            $this->logger->error('Failed to create story', [
                'status_code' => $response->getResponseStatusCode(),
                'error_message' => $response->getErrorMessage(),
                'story_name' => $storyData->name(),
            ]);

            throw new StoryblokApiException(
                sprintf(
                    'Failed to create story: %s (Status code: %d)',
                    $response->getErrorMessage(),
                    $response->getResponseStatusCode(),
                ),
                $response->getResponseStatusCode(),
            );
        } catch (\Exception $exception) {
            if ($exception instanceof StoryblokApiException) {
                throw $exception;
            }

            $this->logger->error('Unexpected error while creating story', [
                'error' => $exception->getMessage(),
                'story_name' => $storyData->name(),
            ]);

            throw new StoryblokApiException(
                'Failed to create story: ' . $exception->getMessage(),
                0,
                $exception,
            );
        }
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
