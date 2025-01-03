<?php

declare(strict_types=1);

namespace Storyblok\Mapi\Endpoints;

use Storyblok\Mapi\StoryblokResponseInterface;

/**
 *
 */
class GenericApi extends EndpointBase
{
    public function get(string $path = "spaces"): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "GET",
            '/v1/' . $path,
        );
    }


    /**
     * @param array<mixed> $payload
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function post(string $path, array $payload = []): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "POST",
            "/v1/" . $path,
            [
                "body" => $payload,
            ],
        );
    }

    public function delete(string $path): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "DELETE",
            '/v1/' . $path,
        );
    }


}
