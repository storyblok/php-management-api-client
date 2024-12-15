<?php

namespace Roberto\Storyblok\Mapi\Endpoints;

use Roberto\Storyblok\Mapi\Endpoints\EndpointBase;

class SpaceApi extends EndpointBase
{
    public function all()
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

    public function get($spaceId)
    {
        return $this->makeRequest(
            "GET",
            "/v1/spaces/{$spaceId}",
        );
    }
    public function getArray($spaceId)
    {
        return $this->get($spaceId)->toArray();
    }

    public function create($payload)
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

    public function delete($spaceId)
    {
        return $this->makeRequest(
            "DELETE",
            "/v1/spaces/{$spaceId}",
        );
    }
}
