<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi;

use Storyblok\ManagementApi\Data\Enum\Region;
use Storyblok\ManagementApi\Endpoints\AssetApi;
use Storyblok\ManagementApi\Endpoints\ComponentApi;
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
use Symfony\Component\HttpClient\Retry\GenericRetryStrategy;
use Symfony\Component\HttpClient\RetryableHttpClient;

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
        bool $shouldRetry = false,
    ) {
        $baseUriMapi =
            $baseUri ??
            StoryblokUtils::baseUriFromRegionForMapi($region->value);
        $httpClient = HttpClient::create();
        if ($shouldRetry) {
            $httpClient = new RetryableHttpClient(
                $httpClient,
                new GenericRetryStrategy([429]),
            );
        }

        $this->httpClient = $httpClient->withOptions([
            "base_uri" => $baseUriMapi,
            "headers" => [
                "Accept" => "application/json",
                "Content-Type" => "application/json",
                "Authorization" => $personalAccessToken,
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
        if (
            $httpAssetClient instanceof
            \Symfony\Contracts\HttpClient\HttpClientInterface
        ) {
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

    /**
     * @deprecated since 1.1, Use `new StoryApi($managementApiClient, $spaceId, $logger)` instead.
     * @codeCoverageIgnore
     */
    public function storyApi(
        string|int $spaceId,
        ?LoggerInterface $logger = null,
    ): StoryApi {
        trigger_error(
            'Method storyApi() is deprecated since 1.1 and will be removed in 2.0. Use `new StoryApi($managementApiClient, $spaceId, $logger)` instead.',
            E_USER_DEPRECATED,
        );

        return new StoryApi($this, $spaceId, $logger ?? new NullLogger());
    }

    /**
     * @deprecated sing 1.1 Use `new StoryBulkApi($managementApiClient, $spaceId, $logger)` instead.
     * @codeCoverageIgnore
     */
    public function storyBulkApi(
        string|int $spaceId,
        ?LoggerInterface $logger = null,
    ): StoryBulkApi {
        trigger_error(
            'Method storyBulkApi() is deprecated since 1.1 and will be removed in 2.0. Use `new StoryBulkApi($managementApiClient, $spaceId, $logger)` instead.',
            E_USER_DEPRECATED,
        );

        return new StoryBulkApi($this, $spaceId, $logger ?? new NullLogger());
    }

    /**
     * @deprecated since 1.1 Use `new AssetApi($managementApiClient, $spaceId)` instead.
     * @codeCoverageIgnore
     */
    public function assetApi(string|int $spaceId): AssetApi
    {
        trigger_error(
            'Method assetApi() is deprecated since 1.1 and will be removed in 2.0. Use `new AssetApi($managementApiClient, $spaceId)` instead.',
            E_USER_DEPRECATED,
        );

        return new AssetApi($this, $spaceId);
    }

    /**
     * @deprecated since 1.1 Use `new TagApi($managementApiClient, $spaceId)` instead.
     * @codeCoverageIgnore
     */
    public function tagApi(string|int $spaceId): TagApi
    {
        trigger_error(
            'Method tagApi() is deprecated since 1.1 and will be removed in 2.0. Use `new TagApi($managementApiClient, $spaceId)` instead.',
            E_USER_DEPRECATED,
        );

        return new TagApi($this, $spaceId);
    }

    /**
     * @deprecated since 1.1 Use `new WorkflowApi($managementApiClient, $spaceId)` instead.
     * @codeCoverageIgnore
     */
    public function workflowApi(string|int $spaceId): WorkflowApi
    {
        trigger_error(
            'Method workflowApi() is deprecated since 1.1 and will be removed in 2.0. Use `new WorkflowApi($managementApiClient, $spaceId)` instead.',
            E_USER_DEPRECATED,
        );

        return new WorkflowApi($this, $spaceId);
    }

    /**
     * @deprecated since 1.1 Use `new WorkflowStageApi($managementApiClient, $spaceId)` instead.
     * @codeCoverageIgnore
     */
    public function workflowStageApi(string|int $spaceId): WorkflowStageApi
    {
        trigger_error(
            'Method workflowStageApi() is deprecated since 1.1 and will be removed in 2.0. Use `new WorkflowStageApi($managementApiClient, $spaceId)` instead.',
            E_USER_DEPRECATED,
        );

        return new WorkflowStageApi($this, $spaceId);
    }

    /**
     * @deprecated since 1.1 Use `new ComponentApi($managementApiClient, $spaceId, $logger)` instead.
     * @codeCoverageIgnore
     */
    public function componentApi(
        string|int $spaceId,
        ?LoggerInterface $logger = null,
    ): ComponentApi {
        trigger_error(
            'Method componentApi() is deprecated since 1.1 and will be removed in 2.0. Use `new ComponentApi($managementApiClient, $spaceId, $logger)` instead.',
            E_USER_DEPRECATED,
        );

        return new ComponentApi($this, $spaceId, $logger ?? new NullLogger());
    }

    /**
     * @deprecated since 1.1 Use `new ManagementApi($managementApiClient)` instead.
     * @codeCoverageIgnore
     */
    public function managementApi(): ManagementApi
    {
        trigger_error(
            'Method managementApi() is deprecated since 1.1 and will be removed in 2.0. Use `new ManagementApi($managementApiClient)` instead.',
            E_USER_DEPRECATED,
        );

        return new ManagementApi($this);
    }
}
