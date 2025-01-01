<?php

declare(strict_types=1);

namespace Roberto\Storyblok\Mapi\Endpoints;

use Roberto\Storyblok\Mapi\Data\AssetData;
use Roberto\Storyblok\Mapi\Data\AssetsData;
use Roberto\Storyblok\Mapi\Data\SpaceData;
use Roberto\Storyblok\Mapi\Data\SpacesData;
use Roberto\Storyblok\Mapi\Data\StoryblokData;
use Roberto\Storyblok\Mapi\Data\UserData;
use Roberto\Storyblok\Mapi\StoryblokResponseInterface;

/**
 *
 */
class AssetApi extends EndpointSpace
{
    public function page(int $page = 1, int $perPage = 25): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "GET",
            '/v1/spaces/' . $this->spaceId . '/assets',
            dataClass: AssetsData::class,
        );
    }

    public function get(string|int $assetId): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "GET",
            '/v1/spaces/' . $this->spaceId . '/assets/' . $assetId,
            dataClass: AssetData::class,
        );
    }



}
