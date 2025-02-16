<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Storyblok\ManagementApi\Data\Stories;
use Storyblok\ManagementApi\Data\Story;
use Storyblok\ManagementApi\Exceptions\InvalidStoryDataException;
use Storyblok\ManagementApi\Exceptions\StoryblokApiException;
use Storyblok\ManagementApi\ManagementApiClient;
use Storyblok\ManagementApi\QueryParameters\Filters\QueryFilters;
use Storyblok\ManagementApi\QueryParameters\PaginationParams;
use Storyblok\ManagementApi\QueryParameters\StoriesParams;
use Storyblok\ManagementApi\Response\SpaceResponse;
use Storyblok\ManagementApi\Response\StoriesResponse;
use Storyblok\ManagementApi\Response\StoryResponse;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

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
    ): StoriesResponse {
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

        $httpResponse = $this->makeHttpRequest(
            "GET",
            $this->buildStoriesEndpoint(),
            options: $options
        );
        return new StoriesResponse($httpResponse);
    }

    /**
     * Retrieves a specific story by ID
     *
     * @throws StoryblokApiException
     */
    public function get(string $storyId): StoryResponse
    {
        $this->validateStoryId($storyId);

        $httpResponse = $this->makeHttpRequest(
            "GET",
            $this->buildStoryEndpoint($storyId),
        );
        return new StoryResponse($httpResponse);

    }

    /**
     * Creates a new story
     *
     * @throws InvalidStoryDataException
     * @throws StoryblokApiException
     * @throws TransportExceptionInterface
     */
    public function create(Story $storyData): StoryResponse
    {
        $this->validateStoryData($storyData);

        if (!$storyData->hasKey("content")) {
            $storyData->setContent([
                "component" => $storyData->defaultContentType(),
            ]);
        }

        try {
            $httpResponse = $this->makeHttpRequest(
                "POST",
                $this->buildStoriesEndpoint(),
                [
                    "body" => json_encode(["story" => $storyData->toArray()]),
                ]
            );

            return new StoryResponse($httpResponse);

        } catch (\Exception $exception) {
            if ($exception instanceof StoryblokApiException) {

                $this->logger->info('xxxFailed to create story', [
                    'status_code' => $exception->getCode(),
                    'error_message' => $exception->getMessage(),
                    'story_name' => $storyData->name(),
                ]);
                throw $exception;
            }

            if ($exception instanceof ClientException) {
                $this->logger->info($exception->getResponse()->getContent(false), [
                    'status_code' => $exception->getCode(),
                    'error_message' => $exception->getMessage(),
                    'story_name' => $storyData->name(),
                ]);
                throw $exception;
            }

            $this->logger->error($exception->getMessage(), [
                'error' => $exception->getMessage(),
                'story_name' => $storyData->name(),
            ]);
            throw $exception;
        }
    }

    /**
     * Updates an existing story
     *
     * @throws InvalidStoryDataException
     */
    public function update(string $storyId, Story $storyData): StoryResponse
    {
        $this->validateStoryId($storyId);
        //$this->validateStoryData($storyData);

        $httpResponse = $this->makeHttpRequest(
            "PUT",
            $this->buildStoryEndpoint($storyId),
            [
                "body" => json_encode(["story" => $storyData->toArray()]),
            ]
        );
        return new StoryResponse($httpResponse);
    }

    public function publish(
        string $storyId,
        int|string|null $release_id = null,
        string|null $language = null,
    ): StoryResponse {
        $this->validateStoryId($storyId);
        //$this->validateStoryData($storyData);

        $queryParams = [];
        if (null !== $release_id) {
            $queryParams["release_id"] = $release_id;
        }

        if (null !== $language) {
            $queryParams["lang"] = $language;
        }

        $httpResponse = $this->makeHttpRequest(
            "GET",
            sprintf('%s/%s/publish', $this->buildStoriesEndpoint(), $storyId),
            [

                "query" => $queryParams,

            ]
        );
        return new StoryResponse($httpResponse);
    }

    public function unpublish(
        string $storyId,
        string|null $language = null,
    ): StoryResponse {
        $this->validateStoryId($storyId);
        //$this->validateStoryData($storyData);

        $queryParams = [];

        if (null !== $language) {
            $queryParams["lang"] = $language;
        }

        $httpResponse = $this->makeHttpRequest(
            "GET",
            sprintf('%s/%s/unpublish', $this->buildStoriesEndpoint(), $storyId),
            [

                "query" => $queryParams,

            ]
        );
        return new StoryResponse($httpResponse);
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
    private function validateStoryData(Story $storyData): void
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
