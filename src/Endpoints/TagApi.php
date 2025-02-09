<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Storyblok\ManagementApi\Data\TagData;
use Storyblok\ManagementApi\Data\TagsData;
use Storyblok\ManagementApi\Response\StoryblokResponseInterface;

class TagApi extends EndpointSpace
{
    public function page(int $page = 1, int $perPage = 25): StoryblokResponseInterface
    {
        $options = [
            'query' => [
                'page' => $page,
                'per_page' => $perPage,
            ],
        ];
        return $this->makeRequest(
            "GET",
            '/v1/spaces/' . $this->spaceId . '/tags',
            options: $options,
            dataClass: TagsData::class,
        );
    }

    public function get(string $name): StoryblokResponseInterface
    {

        return $this->makeRequest(
            "GET",
            '/v1/spaces/' . $this->spaceId . '/tags/' . $name,
            dataClass: TagData::class,
        );
    }

    /**
     * @param $name
     */
    public function delete(string $name): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "DELETE",
            '/v1/spaces/' . $this->spaceId . '/tags/' . $name,
        );
    }

    public function create(string $name): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "POST",
            "/v1/spaces/" . $this->spaceId . '/tags',
            [
                "body" => [
                    "tag" => [
                        "name" => $name,
                    ],
                ],
            ],
            dataClass: TagData::class,
        );
    }

    public function update(string $name, string $newName): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "POST",
            "/v1/spaces/" . $this->spaceId . '/tags/' . $name,
            [
                "body" => [
                    "name" => $newName,
                ],
            ],
            dataClass: TagData::class,
        );
    }
}
