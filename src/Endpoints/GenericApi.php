<?php

declare(strict_types=1);

namespace Roberto\Storyblok\Mapi\Endpoints;


use Roberto\Storyblok\Mapi\StoryblokResponseInterface;

/**
 *
 */
class GenericApi extends EndpointBase
{
    public function get($path = "spaces"): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "GET",
            '/v1/' . $path,
        );
    }




    public function post( string $path, array $payload = []): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "POST",
            "/v1/" . $path,
            [
                "body" => $payload,
            ],
        );
    }

    public function delete($path): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "DELETE",
            '/v1/' . $path,
        );
    }


}
