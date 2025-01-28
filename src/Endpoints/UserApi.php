<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Storyblok\ManagementApi\Data\SpaceData;
use Storyblok\ManagementApi\Data\SpacesData;
use Storyblok\ManagementApi\Data\StoryblokData;
use Storyblok\ManagementApi\Data\UserData;
use Storyblok\ManagementApi\StoryblokResponseInterface;

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
