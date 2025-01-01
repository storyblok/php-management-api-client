<?php

declare(strict_types=1);

namespace Roberto\Storyblok\Mapi\Endpoints;

use Roberto\Storyblok\Mapi\Data\StoriesData;
use Roberto\Storyblok\Mapi\Data\StoryblokData;
use Roberto\Storyblok\Mapi\Endpoints\EndpointBase;
use Roberto\Storyblok\Mapi\StoryblokResponseInterface;

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
