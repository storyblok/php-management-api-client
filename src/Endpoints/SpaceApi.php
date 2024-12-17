<?php

namespace Roberto\Storyblok\Mapi\Endpoints;

use Roberto\Storyblok\Mapi\Endpoints\EndpointBase;
use Roberto\Storyblok\Mapi\StoryblokResponseInterface;

class SpaceApi extends EndpointBase
{
    public function all(): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "GET",
            '/v1/spaces',
        );
    }

    public function allArray()
    {
        return $this->all()->toArray();
    }

    public function get($spaceId): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "GET",
            "/v1/spaces/{$spaceId}",
        );
    }
    public function getArray($spaceId): array
    {
        return $this->get($spaceId)->toArray();
    }

    public function create($payload): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "POST",
            "/v1/spaces",
            [
                "body" => [
                    "space" => $payload,
                ],
            ],
        );
    }

    public function delete($spaceId): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "DELETE",
            "/v1/spaces/{$spaceId}",
        );
    }
}
