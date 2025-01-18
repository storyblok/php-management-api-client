<?php

declare(strict_types=1);

namespace Storyblok\Mapi\Endpoints;

use Storyblok\Mapi\StoryblokResponseInterface;

/**
 *
 */
class ManagementApi extends EndpointBase
{
    /**
     * @param string $path the path of the API endpoint, for example spaces or spaces/1111/stories
     * @param array<mixed> $queryParams
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function get(string $path = "spaces", array $queryParams = []): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "GET",
            '/v1/' . $path,
            [
                "query" => $queryParams,
            ],
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
