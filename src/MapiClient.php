<?php

declare(strict_types=1);

namespace Storyblok\Mapi;

use Storyblok\Mapi\Data\Enum\Region;
use Storyblok\Mapi\Endpoints\AssetApi;
use Storyblok\Mapi\Endpoints\ManagementApi;
use Storyblok\Mapi\Endpoints\SpaceApi;
use Storyblok\Mapi\Endpoints\StoryApi;
use Storyblok\Mapi\Endpoints\UserApi;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MapiClient
{
    private HttpClientInterface $httpClient;

    public function __construct(
        string $personalAccessToken,
        Region $region = Region::EU,
        ?string $baseUri = null,
    ) {
        $baseUriMapi = $baseUri ?? StoryblokUtils::baseUriFromRegionForMapi($region->value);
        $this->httpClient = HttpClient::create()
            ->withOptions([
                'base_uri' => $baseUriMapi,
                'headers' =>
                    [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => $personalAccessToken,
                    ],
            ]);
    }



    public static function init(
        string $personalAccessToken,
        Region $region = Region::EU,
        ?string $baseUri = null,
    ): self {

        return new self($personalAccessToken, $region, $baseUri);

    }

    public static function initTest(
        HttpClientInterface $httpClient,
    ): self {

        $client = new self("");
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

    public function managementApi(): ManagementApi
    {
        return new ManagementApi($this->httpClient);
    }
}
