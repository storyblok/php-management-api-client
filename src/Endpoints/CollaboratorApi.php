<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Storyblok\ManagementApi\QueryParameters\CollaboratorsParams;
use Storyblok\ManagementApi\QueryParameters\PaginationParams;
use Storyblok\ManagementApi\Response\CollaboratorsResponse;

class CollaboratorApi extends EndpointSpace
{
    public function page(
        ?CollaboratorsParams $params = null,
        ?PaginationParams $page = null,
    ): CollaboratorsResponse {
        if (!$params instanceof CollaboratorsParams) {
            $params = new CollaboratorsParams();
        }

        if (!$page instanceof PaginationParams) {
            $page = new PaginationParams();
        }

        $options = [
            'query' => array_merge($params->toArray(), $page->toArray()),
        ];
        $httpResponse = $this->makeHttpRequest(
            "GET",
            '/v1/spaces/' . $this->spaceId . '/collaborators/',
            options: $options
        );
        return new CollaboratorsResponse($httpResponse);
    }
}
