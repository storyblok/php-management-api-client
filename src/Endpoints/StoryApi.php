<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Storyblok\ManagementApi\Data\Stories;
use Storyblok\ManagementApi\Data\Story;
use Storyblok\ManagementApi\Data\StoryComponent;
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
            "query" => array_merge(
                $params->toArray(),
                $queryFilters->toArray(),
                $page->toArray(),
            ),
        ];

        $httpResponse = $this->makeHttpRequest(
            "GET",
            $this->buildStoriesEndpoint(),
            options: $options,
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
     * @param bool $publish set as true if you want to publish the story immediatly
     * @param int $releaseId set the release id if you want to create the story in a specific release
     */
    public function create(
        Story $storyData,
        bool $publish = false,
        int $releaseId = 0,
    ): StoryResponse {
        $this->validateStoryData($storyData);

        if (!$storyData->hasKey("content")) {
            $storyData->setContent(
                new StoryComponent($storyData->defaultContentType()),
            );
        }

        $payload = ["story" => $storyData->toArray()];
        if ($publish) {
            $payload["publish"] = 1;
        }

        if ($releaseId > 0) {
            $payload["release_id"] = $releaseId;
        }

        $httpResponse = $this->makeHttpRequest(
            "POST",
            $this->buildStoriesEndpoint(),
            [
                "body" => json_encode($payload),
            ],
        );

        return new StoryResponse($httpResponse);
    }

    /**
     * Updates an existing story.
     *
     * @param string $storyId
     *   The ID of the story to update.
     *
     * @param Story $storyData
     *   The Story object containing the updated content and metadata.
     *
     * @param string $groupId
     *   Optional. Group ID (UUID string) shared between stories defined as alternates.
     *
     * @param string $forceUpdate
     *   Optional. Set to "1" to force an update of a locked story.
     *   A story is locked when another user edits it. Forcing an update may cause
     *   a content conflict. This parameter has no effect if the story is locked
     *   due to a workflow stage.
     *
     * @param int $releaseId
     *   Optional. Numeric ID of the release in which the story should be updated.
     *
     * @param bool $publish
     *   Optional. Set to true to publish the story immediately after updating it.
     *
     * @param string $lang
     *   Optional. Language code to update or publish the story individually.
     *   The language must be enabled in Settings → Internationalization.
     *
     * @return StoryResponse
     *   The response containing the updated story data.
     *
     * @throws InvalidStoryDataException
     *   Thrown when the provided story data is invalid.
     */
    public function update(
        string $storyId,
        Story $storyData,
        string $groupId = "",
        string $forceUpdate = "",
        int $releaseId = 0,
        bool $publish = false,
        string $lang = "",
    ): StoryResponse {
        $this->validateStoryId($storyId);
        //$this->validateStoryData($storyData);
        $payload = ["story" => $storyData->toArray()];

        if ($groupId !== "") {
            $payload["group_id"] = $groupId;
        }

        if ($forceUpdate !== "") {
            $payload["force_update"] = $forceUpdate;
        }

        if ($releaseId > 0) {
            $payload["release_id"] = $releaseId;
        }

        if ($publish) {
            $payload["publish"] = 1;
        }

        if ($lang !== "") {
            $payload["lang"] = $lang;
        }

        $httpResponse = $this->makeHttpRequest(
            "PUT",
            $this->buildStoryEndpoint($storyId),
            [
                "body" => json_encode($payload),
            ],
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
            sprintf("%s/%s/publish", $this->buildStoriesEndpoint(), $storyId),
            [
                "query" => $queryParams,
            ],
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
            sprintf("%s/%s/unpublish", $this->buildStoriesEndpoint(), $storyId),
            [
                "query" => $queryParams,
            ],
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
            throw new \InvalidArgumentException(
                "Page number must be greater than 0",
            );
        }

        if ($page->perPage() < 1) {
            throw new \InvalidArgumentException(
                "Items per page must be greater than 0",
            );
        }
    }

    /**
     * Validates story ID
     *
     * @throws \InvalidArgumentException
     */
    private function validateStoryId(string $storyId): void
    {
        if ($storyId === "" || $storyId === "0") {
            throw new \InvalidArgumentException("Story ID cannot be empty");
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
            throw new InvalidStoryDataException("Invalid story data provided");
        }
    }

    /**
     * Builds the base endpoint for stories
     */
    private function buildStoriesEndpoint(): string
    {
        return sprintf("/v1/spaces/%s/stories", $this->spaceId);
    }

    /**
     * Builds the endpoint for a specific story
     */
    private function buildStoryEndpoint(string $storyId): string
    {
        return sprintf("%s/%s", $this->buildStoriesEndpoint(), $storyId);
    }
}
