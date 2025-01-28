<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class EndpointSpace
 * @package Storyblok\ManagementApi\Endpoints
 */
class EndpointSpace extends EndpointBase
{
    public function __construct(
        protected HttpClientInterface $httpClient,
        protected string|int $spaceId,
    ) {
        parent::__construct($httpClient);
    }
}
