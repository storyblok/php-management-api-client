<?php

declare(strict_types=1);

namespace Roberto\Storyblok\Mapi\Endpoints;

use Roberto\Storyblok\Mapi\StoryblokResponse;
use Roberto\Storyblok\Mapi\StoryblokResponseInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EndpointBase
{
    public function __construct(protected ?HttpClientInterface $clientMapi) {}



    public function makeRequest(
        string $method = "GET",
        string $path = "/v1/spaces",
        array $options = [],
    ): StoryblokResponseInterface {
        $response = $this->clientMapi->request(
            $method,
            $path,
            $options,
        );

        return StoryblokResponse::make($response);

    }

}
