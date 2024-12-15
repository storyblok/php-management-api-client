<?php

namespace Roberto\Storyblok\Mapi;

use Roberto\Storyblok\Mapi\Endpoints\SpaceApi;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MapiClient {

    private ?HttpClientInterface $clientMapi = null;

    private ?string $personalAccessToken = null;


    public function init(
        string $personalAccessToken,
        string $region = "EU",
        ?string $baseUri = null,
    ): void
    {


        $baseUriMapi = $baseUri ?? StoryblokUtils::baseUriFromRegionForMapi($region);


        $this->clientMapi = HttpClient::create()
            ->withOptions([
                'base_uri' => $baseUriMapi,
                'headers' =>
                    [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => $this->personalAccessToken,
                    ],
            ]);


    }

    public function spaceApi(): SpaceApi {
        return new SpaceApi($this->clientMapi);
    }
}
