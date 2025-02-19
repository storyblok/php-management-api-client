<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Storyblok\ManagementApi\Data\Tag;
use Storyblok\ManagementApi\Data\Tags;
use Storyblok\ManagementApi\Response\StoryblokResponseInterface;
use Storyblok\ManagementApi\Response\TagResponse;
use Storyblok\ManagementApi\Response\TagsResponse;

class TagApi extends EndpointSpace
{
    public function page(int $page = 1, int $perPage = 25): TagsResponse
    {
        $options = [
            'query' => [
                'page' => $page,
                'per_page' => $perPage,
            ],
        ];
        $httpResponse = $this->makeHttpRequest(
            "GET",
            '/v1/spaces/' . $this->spaceId . '/tags',
            options: $options
        );
        return new TagsResponse($httpResponse);
    }

    /**
     * @param string $name the tag name in string format
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function get(string $name): TagResponse
    {

        $httpResponse = $this->makeHttpRequest(
            "GET",
            '/v1/spaces/' . $this->spaceId . '/tags/' . $name
        );

        return new TagResponse($httpResponse);
    }

    /**
     * @param $name
     */
    public function delete(string $name): TagResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "DELETE",
            '/v1/spaces/' . $this->spaceId . '/tags/' . $name
        );
        return new TagResponse($httpResponse);
    }

    public function create(string $name): TagResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "POST",
            "/v1/spaces/" . $this->spaceId . '/tags',
            [
                "body" => [
                    "tag" => [
                        "name" => $name,
                    ],
                ],
            ]
        );
        return new TagResponse($httpResponse);
    }

    public function update(string $name, string $newName): TagResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "POST",
            "/v1/spaces/" . $this->spaceId . '/tags/' . $name,
            [
                "body" => [
                    "name" => $newName,
                ],
            ]
        );
        return new TagResponse($httpResponse);
    }
}
