<?php

declare(strict_types=1);

namespace Storyblok\Mapi\Endpoints;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class EndpointSpace
 * @package Storyblok\Mapi\Endpoints
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
