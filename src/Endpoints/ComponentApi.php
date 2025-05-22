<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Storyblok\ManagementApi\Data\Component;
use Storyblok\ManagementApi\Exceptions\InvalidComponentDataException;
use Storyblok\ManagementApi\Exceptions\InvalidStoryDataException;
use Storyblok\ManagementApi\Exceptions\StoryblokApiException;
use Storyblok\ManagementApi\ManagementApiClient;
use Storyblok\ManagementApi\QueryParameters\ComponentsParams;
use Storyblok\ManagementApi\QueryParameters\PaginationParams;
use Storyblok\ManagementApi\Response\ComponentResponse;
use Storyblok\ManagementApi\Response\ComponentsResponse;
use Storyblok\ManagementApi\Response\StoriesResponse;
use Storyblok\ManagementApi\Response\StoryResponse;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * ComponentApi handles all component-related operations in the Storyblok Management API
 *
 * This class provides methods to create, read, update and list components
 * through the Storyblok Management API.
 */
class ComponentApi extends EndpointSpace
{
    /**
     * ComponentApi constructor.
     */
    public function __construct(
        ManagementApiClient $managementClient,
        string|int $spaceId,
        LoggerInterface $logger = new NullLogger(),
    ) {
        parent::__construct($managementClient, $spaceId, $logger);
    }

    /**
     * Retrieves all the space components
     */
    public function all(
        ?ComponentsParams $params = null
    ): ComponentsResponse {

        $paramsArray = ($params instanceof ComponentsParams) ? $params->toArray() : [];

        $options = [
            'query' => $paramsArray,
        ];

        $httpResponse = $this->makeHttpRequest(
            "GET",
            $this->buildComponentsEndpoint(),
            options: $options
        );
        return new ComponentsResponse($httpResponse);
    }

    /**
     * Retrieves a specific component by ID
     *
     * @throws StoryblokApiException
     */
    public function get(string $componentId): ComponentResponse
    {
        $this->validateComponentId($componentId);

        $httpResponse = $this->makeHttpRequest(
            "GET",
            $this->buildComponentEndpoint($componentId),
        );
        return new ComponentResponse($httpResponse);

    }

    /**
     * Creates a new component
     *
     * @throws InvalidComponentDataException
     * @throws StoryblokApiException
     * @throws TransportExceptionInterface
     */
    public function create(Component $componentData): ComponentResponse
    {
        $this->validateComponentData($componentData);

        $httpResponse = $this->makeHttpRequest(
            "POST",
            $this->buildComponentsEndpoint(),
            [
                "body" => json_encode(["component" => $componentData->toArray()]),
            ]
        );

        return new ComponentResponse($httpResponse);

    }

    /**
     * Updates an existing component
     *
     * @throws InvalidComponentDataException
     */
    public function update(string $componentId, Component $componentData): ComponentResponse
    {
        $this->validateComponentId($componentId);
        //$this->validateStoryData($storyData);

        $httpResponse = $this->makeHttpRequest(
            "PUT",
            $this->buildComponentEndpoint($componentId),
            [
                "body" => json_encode(["component" => $componentData->toArray()]),
            ]
        );
        return new ComponentResponse($httpResponse);
    }

    /**
     * Validates component ID
     *
     * @throws \InvalidArgumentException
     */
    private function validateComponentId(string $componentId): void
    {
        if ($componentId === '' || $componentId === '0') {
            throw new \InvalidArgumentException('Component ID cannot be empty');
        }
    }

    /**
     * Validates component data
     *
     * @throws InvalidComponentDataException
     */
    private function validateComponentData(Component $componentData): void
    {
        if (!$componentData->isValid()) {
            throw new InvalidStoryDataException('Invalid component data provided');
        }
    }

    /**
     * Builds the base endpoint for components
     */
    private function buildComponentsEndpoint(): string
    {
        return sprintf('/v1/spaces/%s/components', $this->spaceId);
    }

    /**
     * Builds the endpoint for a specific component
     */
    private function buildComponentEndpoint(string $componentId): string
    {
        return sprintf('%s/%s', $this->buildComponentsEndpoint(), $componentId);
    }
}
