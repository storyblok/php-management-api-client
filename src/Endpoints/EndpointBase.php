<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Storyblok\ManagementApi\Data\StoryblokData;
use Storyblok\ManagementApi\ManagementApiClient;
use Storyblok\ManagementApi\StoryblokResponse;
use Storyblok\ManagementApi\StoryblokResponseInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class EndpointBase
 * @package Storyblok\ManagementApi\Endpoints
 */
class EndpointBase
{
    protected HttpClientInterface $httpClient;

    public function __construct(
        protected ManagementApiClient $managementClient,
        protected LoggerInterface $logger = new NullLogger(),
    ) {
        $this->httpClient = $managementClient->httpClient();
    }


    /**
     * @param array<mixed> $options
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function makeRequest(
        string $method = "GET",
        string $path = "/v1/spaces",
        array $options = [],
        string $dataClass = StoryblokData::class,
    ): StoryblokResponseInterface {
        $response = $this->httpClient->request(
            $method,
            $path,
            $options,
        );

        return StoryblokResponse::make($response, $dataClass);
    }
}
