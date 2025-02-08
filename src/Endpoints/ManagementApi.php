<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Storyblok\ManagementApi\Response\StoryblokResponseInterface;

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

    /**
     * Function for updating a resource.
     * Under the hood, is performed a PUT HTTP method
     * @param string $path the path of the API endpoint,
     *        for example: spaces/1111/stories/22222
     * @param array<mixed> $payload the Request Body Properties
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function put(string $path, array $payload = []): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "PUT",
            "/v1/" . $path,
            [
                "body" => $payload,
            ],
        );
    }


}
