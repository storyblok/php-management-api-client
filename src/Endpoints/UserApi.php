<?php

declare(strict_types=1);

namespace Storyblok\Mapi\Endpoints;

use Storyblok\Mapi\Data\SpaceData;
use Storyblok\Mapi\Data\SpacesData;
use Storyblok\Mapi\Data\StoryblokData;
use Storyblok\Mapi\Data\UserData;
use Storyblok\Mapi\StoryblokResponseInterface;

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
