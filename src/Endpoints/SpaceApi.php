<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Storyblok\ManagementApi\Data\SpaceData;
use Storyblok\ManagementApi\Data\SpacesData;
use Storyblok\ManagementApi\Data\StoryblokData;
use Storyblok\ManagementApi\StoryblokResponseInterface;

/**
 *
 */
class SpaceApi extends EndpointBase
{
    public function all(): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "GET",
            '/v1/spaces',
            dataClass: SpacesData::class,
        );
    }


    public function get(string $spaceId): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "GET",
            '/v1/spaces/' . $spaceId,
            dataClass: SpaceData::class,
        );
    }


    public function create(StoryblokData $storyblokData): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "POST",
            "/v1/spaces",
            [
                "body" => [
                    "space" => $storyblokData->toArray(),
                ],
            ],
        );
    }

    public function duplicate(string|int $duplicateId, string $name): StoryblokResponseInterface
    {
        return $this->makeRequest(
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
    }

    /**
     * @param $spaceId
     */
    public function delete(string $spaceId): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "DELETE",
            '/v1/spaces/' . $spaceId,
        );
    }

    /**
     * @param $spaceId
     */
    public function backup(string $spaceId): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "POST",
            sprintf('/v1/spaces/%s/backups', $spaceId),
            [
                "body" => [
                ],
            ],
        );
    }
}
