<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi;

use Storyblok\ManagementApi\Data\Enum\Region;
use Storyblok\ManagementApi\Endpoints\AssetApi;
use Storyblok\ManagementApi\Endpoints\ManagementApi;
use Storyblok\ManagementApi\Endpoints\SpaceApi;
use Storyblok\ManagementApi\Endpoints\StoryApi;
use Storyblok\ManagementApi\Endpoints\TagApi;
use Storyblok\ManagementApi\Endpoints\UserApi;
use Storyblok\ManagementApi\Endpoints\WorkflowApi;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class MapiClient
 * @package Storyblok\ManagementApi
 */
class ManagementApiClient
{
    private HttpClientInterface $httpClient;

    /**
     * MapiClient constructor.
     */
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

    /**
     * Initialize the MapiClient
     */
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

    public function storyApi(string|int $spaceId, ?LoggerInterface $logger = null): StoryApi
    {
        return new StoryApi(
            $this->httpClient,
            $spaceId,
            $logger ?? new NullLogger(),
        );
    }

    public function userApi(): UserApi
    {
        return new UserApi($this->httpClient);
    }

    public function assetApi(string|int $spaceId): AssetApi
    {
        return new AssetApi($this->httpClient, $spaceId);
    }

    public function tagApi(string|int $spaceId): TagApi
    {
        return new TagApi($this->httpClient, $spaceId);
    }

    public function workflowApi(string|int $spaceId): WorkflowApi
    {
        return new WorkflowApi($this->httpClient, $spaceId);
    }

    public function managementApi(): ManagementApi
    {
        return new ManagementApi($this->httpClient);
    }
}
