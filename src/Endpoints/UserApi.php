<?php

declare(strict_types=1);

namespace Roberto\Storyblok\Mapi\Endpoints;

use Roberto\Storyblok\Mapi\Data\SpaceData;
use Roberto\Storyblok\Mapi\Data\SpacesData;
use Roberto\Storyblok\Mapi\Data\StoryblokData;
use Roberto\Storyblok\Mapi\Data\UserData;
use Roberto\Storyblok\Mapi\StoryblokResponseInterface;

/**
 *
 */
class UserApi extends EndpointBase
{
    public function me(): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "GET",
            '/v1/users/me',
            dataClass: UserData::class,
        );
    }



}
