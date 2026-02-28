<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Storyblok\ManagementApi\Data\Apps;
use Storyblok\ManagementApi\QueryParameters\AppsParams;
use Storyblok\ManagementApi\Response\AppResponse;
use Storyblok\ManagementApi\Response\AppsResponse;

class AppApi extends EndpointBase
{
    protected const string API_PATH_APPS = "/v1/apps";

    public function page(AppsParams $params): AppsResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "GET",
            self::API_PATH_APPS,
            options: [
                'query' => $params->toArray(),
            ],
        );

        return new AppsResponse($httpResponse, Apps::class);
    }

    public function get(string|int $appId, string|int $spaceId): AppResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "GET",
            sprintf('%s/%s', self::API_PATH_APPS, $appId),
            options: [
                'query' => ['space_id' => $spaceId],
            ],
        );

        return new AppResponse($httpResponse);
    }
}
