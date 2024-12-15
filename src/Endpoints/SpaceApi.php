<?php

namespace Roberto\Storyblok\Mapi\Endpoints;

use Roberto\Storyblok\Mapi\Endpoints\EndpointBase;

class SpaceApi extends EndpointBase
{
    public function all()
    {
        return $this->makeRequest(
            "GET",
            '/spaces',
        );
    }
}
