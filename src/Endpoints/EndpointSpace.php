<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class EndpointSpace
 * @package Storyblok\ManagementApi\Endpoints
 */
class EndpointSpace extends EndpointBase
{
    public function __construct(
        protected ManagementApiClient $managementClient,
        protected string|int $spaceId,
        LoggerInterface $logger = new NullLogger(),
    ) {
        parent::__construct($managementClient, $logger);
    }
}
