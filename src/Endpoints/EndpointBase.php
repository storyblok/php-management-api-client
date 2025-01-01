<?php

declare(strict_types=1);

namespace Roberto\Storyblok\Mapi\Endpoints;

use Roberto\Storyblok\Mapi\Data\StoryblokData;
use Roberto\Storyblok\Mapi\StoryblokResponse;
use Roberto\Storyblok\Mapi\StoryblokResponseInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EndpointBase
{
    public function __construct(protected ?HttpClientInterface $httpClient) {}


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
