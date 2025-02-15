<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Storyblok\ManagementApi\Data\SpaceData;
use Storyblok\ManagementApi\Data\SpacesData;
use Storyblok\ManagementApi\Data\StoryblokData;
use Storyblok\ManagementApi\Response\SpaceResponse;
use Storyblok\ManagementApi\Response\SpacesResponse;
use Storyblok\ManagementApi\Response\StoryblokResponse;
use Storyblok\ManagementApi\Response\StoryblokResponseInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class SpaceApi extends EndpointBase
{
    public function all(): SpacesResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "GET",
            '/v1/spaces',
        );
        return new SpacesResponse($httpResponse, SpacesData::class);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function get(string $spaceId): SpaceResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "GET",
            '/v1/spaces/' . $spaceId,
        );

        return new SpaceResponse($httpResponse);
    }

    public function create(StoryblokData $storyblokData): SpaceResponse
    {

        $httpResponse = $this->makeHttpRequest(
            "POST",
            "/v1/spaces",
            [
                "body" => [
                    "space" => $storyblokData->toArray(),
                ],
            ],
        );
        return new SpaceResponse($httpResponse);
    }

    public function duplicate(string|int $duplicateId, string $name): SpaceResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "POST",
            "/v1/spaces",
            [
                "body" => [
                    "dup_id" => $duplicateId,
                    "space" => [
                        "name" => $name,
                    ],
                ],
            ],
        );
        return new SpaceResponse($httpResponse);
    }

    /**
     * @param $spaceId
     */
    public function delete(string $spaceId): SpaceResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "DELETE",
            '/v1/spaces/' . $spaceId,
        );
        return new SpaceResponse($httpResponse);
    }

    /**
     * @param $spaceId
     */
    public function backup(string $spaceId): SpaceResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "POST",
            sprintf('/v1/spaces/%s/backups', $spaceId),
            [
                "body" => [
                ],
            ],
        );
        return new SpaceResponse($httpResponse);
    }
}
