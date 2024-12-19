<?php

declare(strict_types=1);

namespace Roberto\Storyblok\Mapi\Endpoints;

use Roberto\Storyblok\Mapi\Data\StoryblokData;
use Roberto\Storyblok\Mapi\Endpoints\EndpointBase;
use Roberto\Storyblok\Mapi\StoryblokResponseInterface;

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
        );
    }


    /**
     * @param $spaceId
     */
    public function get(string $spaceId): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "GET",
            '/v1/spaces/' . $spaceId,
        );
    }


    public function create(StoryblokData $payload): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "POST",
            "/v1/spaces",
            [
                "body" => [
                    "space" => $payload->toArray(),
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
    public function backup($spaceId): StoryblokResponseInterface
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
