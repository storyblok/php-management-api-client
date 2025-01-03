<?php

declare(strict_types=1);

namespace Storyblok\Mapi;

use Storyblok\Mapi\Endpoints\AssetApi;
use Storyblok\Mapi\Endpoints\GenericApi;
use Storyblok\Mapi\Endpoints\SpaceApi;
use Storyblok\Mapi\Endpoints\StoryApi;
use Storyblok\Mapi\Endpoints\UserApi;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MapiClient
{
    private ?HttpClientInterface $httpClient = null;


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

        $client->httpClient = HttpClient::create()
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

    public static function initTest(
        HttpClientInterface $httpClient,
    ): self {

        $client = new self();
        //$baseUriMapi = $baseUri ?? StoryblokUtils::baseUriFromRegionForMapi($region);

        $client->httpClient = $httpClient;


        return $client;

    }

    public function spaceApi(): SpaceApi
    {
        return new SpaceApi($this->httpClient);
    }

    public function storyApi(string|int $spaceId): StoryApi
    {
        return new StoryApi($this->httpClient, $spaceId);
    }

    public function userApi(): UserApi
    {
        return new UserApi($this->httpClient);
    }

    public function assetApi(string|int $spaceId): AssetApi
    {
        return new AssetApi($this->httpClient, $spaceId);
    }

    public function genericApi(): GenericApi
    {
        return new GenericApi($this->httpClient);
    }
}
