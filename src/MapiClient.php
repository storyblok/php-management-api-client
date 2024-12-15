<?php

namespace Roberto\Storyblok\Mapi;

use Roberto\Storyblok\Mapi\Endpoints\SpaceApi;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MapiClient
{
    private ?HttpClientInterface $clientMapi = null;


    public static function initEU(string $personalAccessToken): self
    {
        return self::init(
            $personalAccessToken,
            "EU",
        );
    }
    public static function initUS(string $personalAccessToken): self
    {
        return self::init(
            $personalAccessToken,
            "US",
        );
    }
    public static function initAP(string $personalAccessToken): self
    {
        return self::init(
            $personalAccessToken,
            "AP",
        );
    }
    public static function initCA(string $personalAccessToken): self
    {
        return self::init(
            $personalAccessToken,
            "CA",
        );
    }

    public static function initCN(string $personalAccessToken): self
    {
        return self::init(
            $personalAccessToken,
            "CN",
        );
    }


    public static function init(
        string $personalAccessToken,
        string $region = "EU",
        ?string $baseUri = null,
    ): self {

        $client = new self();
        $baseUriMapi = $baseUri ?? StoryblokUtils::baseUriFromRegionForMapi($region);

        $client->clientMapi = HttpClient::create()
            ->withOptions([
                'base_uri' => $baseUriMapi,
                'headers' =>
                    [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => $personalAccessToken,
                    ],
            ]);


        return $client;

    }

    public function spaceApi(): SpaceApi
    {
        return new SpaceApi($this->clientMapi);
    }
}
