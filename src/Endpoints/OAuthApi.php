<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Storyblok\ManagementApi\Response\OAuthUserInfoResponse;
use Storyblok\ManagementApi\StoryblokUtils;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class OAuthApi extends EndpointBase
{
    private const string USER_INFO_PATH = "/oauth/user_info";

    /**
     * Retrieve the authenticated OAuth user information.
     *
     * This endpoint is used by Storyblok OAuth flows, including space plugins.
     * It intentionally targets the regional OAuth host (for example
     * https://api.storyblok.com for EU) instead of the Management API base URI.
     *
     * @throws TransportExceptionInterface
     */
    public function userInfo(): OAuthUserInfoResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "GET",
            $this->userInfoUrl(),
        );

        return new OAuthUserInfoResponse($httpResponse);
    }

    private function userInfoUrl(): string
    {
        return StoryblokUtils::baseUriFromRegionForOauth(
            $this->managementClient->region()->value,
        ) . self::USER_INFO_PATH;
    }
}
