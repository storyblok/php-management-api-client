<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Storyblok\ManagementApi\Data\User;
use Storyblok\ManagementApi\Response\StoryblokResponseInterface;
use Storyblok\ManagementApi\Response\UserResponse;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class UserApi extends EndpointBase
{
    /**
     * @throws TransportExceptionInterface
     */
    public function me(): UserResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "GET",
            '/v1/users/me'
        );
        return new UserResponse($httpResponse);
    }
}
