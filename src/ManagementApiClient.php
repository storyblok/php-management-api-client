<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi;

use Storyblok\ManagementApi\Data\Enum\Region;
use Storyblok\ManagementApi\Endpoints\AssetApi;
use Storyblok\ManagementApi\Endpoints\ManagementApi;
use Storyblok\ManagementApi\Endpoints\SpaceApi;
use Storyblok\ManagementApi\Endpoints\StoryApi;
use Storyblok\ManagementApi\Endpoints\StoryBulkApi;
use Storyblok\ManagementApi\Endpoints\TagApi;
use Storyblok\ManagementApi\Endpoints\UserApi;
use Storyblok\ManagementApi\Endpoints\WorkflowApi;
use Storyblok\ManagementApi\Endpoints\WorkflowStageApi;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\MockHttpClient;
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

    private HttpClientInterface $httpAssetClient;

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
        $this->httpAssetClient = HttpClient::create();
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
        ?HttpClientInterface $httpAssetClient = null,
    ): self {

        $client = new self("");
        //$baseUriMapi = $baseUri ?? StoryblokUtils::baseUriFromRegionForMapi($region);

        $client->httpClient = $httpClient;
        if ($httpAssetClient instanceof \Symfony\Contracts\HttpClient\HttpClientInterface) {
            $client->httpAssetClient = $httpAssetClient;
        } else {
            $client->httpAssetClient = new MockHttpClient();
        }



        return $client;

    }

    public function httpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }

    public function httpAssetClient(): HttpClientInterface
    {
        return $this->httpAssetClient;
    }

    public function spaceApi(): SpaceApi
    {
        return new SpaceApi($this);
    }

    public function storyApi(string|int $spaceId, ?LoggerInterface $logger = null): StoryApi
    {
        return new StoryApi(
            $this,
            $spaceId,
            $logger ?? new NullLogger(),
        );
    }

    public function storyBulkApi(string|int $spaceId, ?LoggerInterface $logger = null): StoryBulkApi
    {
        return new StoryBulkApi(
            $this,
            $spaceId,
            $logger ?? new NullLogger(),
        );
    }

    public function userApi(): UserApi
    {
        return new UserApi($this);
    }

    public function assetApi(string|int $spaceId): AssetApi
    {
        return new AssetApi($this, $spaceId);
    }

    public function tagApi(string|int $spaceId): TagApi
    {
        return new TagApi($this, $spaceId);
    }

    public function workflowApi(string|int $spaceId): WorkflowApi
    {
        return new WorkflowApi($this, $spaceId);
    }

    public function workflowStageApi(string|int $spaceId): WorkflowStageApi
    {
        return new WorkflowStageApi($this, $spaceId);
    }

    public function managementApi(): ManagementApi
    {
        return new ManagementApi($this);
    }
}
