<?php

declare(strict_types=1);

namespace Storyblok\Mapi\Endpoints;

use Storyblok\Mapi\Data\StoriesData;
use Storyblok\Mapi\Data\StoryblokData;
use Storyblok\Mapi\Endpoints\EndpointBase;
use Storyblok\Mapi\StoryblokResponseInterface;

/**
 *
 */
class StoryApi extends EndpointSpace
{
    public function page(int $page = 1, int $perPage = 25): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "GET",
            '/v1/spaces/' . $this->spaceId . '/stories',
            dataClass: StoriesData::class,
        );
    }


    /**
     * @param $storyId
     */
    public function get(string $storyId): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "GET",
            '/v1/spaces/' . $this->spaceId . '/stories/' . $storyId,
        );
    }



}
