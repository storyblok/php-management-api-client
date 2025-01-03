<?php

declare(strict_types=1);

namespace Storyblok\Mapi\Endpoints;

use Storyblok\Mapi\Data\SpaceData;
use Storyblok\Mapi\Data\SpacesData;
use Storyblok\Mapi\Data\StoryblokData;
use Storyblok\Mapi\StoryblokResponseInterface;

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
