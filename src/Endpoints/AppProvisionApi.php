<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Storyblok\ManagementApi\Data\AppProvisions;
use Storyblok\ManagementApi\Response\AppProvisionResponse;
use Storyblok\ManagementApi\Response\AppProvisionsResponse;
use Storyblok\ManagementApi\Response\StoryblokResponse;

class AppProvisionApi extends EndpointSpace
{
    public function page(): AppProvisionsResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "GET",
            '/v1/spaces/' . $this->spaceId . '/app_provisions',
        );

        return new AppProvisionsResponse($httpResponse, AppProvisions::class);
    }

    public function get(string|int $appId): AppProvisionResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "GET",
            '/v1/spaces/' . $this->spaceId . '/app_provisions/' . $appId,
        );

        return new AppProvisionResponse($httpResponse);
    }

    public function install(string|int $appId): AppProvisionResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "POST",
            '/v1/spaces/' . $this->spaceId . '/app_provisions/',
            [
                "body" => [
                    "app_provision" => [
                        "app_id" => (string) $appId,
                    ],
                ],
            ],
        );

        return new AppProvisionResponse($httpResponse);
    }

    public function delete(string|int $appId): StoryblokResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "DELETE",
            '/v1/spaces/' . $this->spaceId . '/app_provisions/' . $appId,
        );

        return new StoryblokResponse($httpResponse);
    }
}
