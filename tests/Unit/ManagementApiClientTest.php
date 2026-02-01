<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use Storyblok\ManagementApi\Data\Enum\Region;
use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\RetryableHttpClient;
use Tests\TestCase;

final class ManagementApiClientTest extends TestCase
{
    public function testConstructorWithDefaultRegion(): void
    {
        $client = new ManagementApiClient(personalAccessToken: "test-token");

        $this->assertInstanceOf(CurlHttpClient::class, $client->httpClient());
        $this->assertInstanceOf(
            CurlHttpClient::class,
            $client->httpAssetClient(),
        );
    }

    /**
     * @return \Iterator<string, array{Region}>
     */
    public static function regionProvider(): \Iterator
    {
        yield "EU region" => [Region::EU];
        yield "US region" => [Region::US];
        yield "CA region" => [Region::CA];
        yield "AP region" => [Region::AP];
        yield "CN region" => [Region::CN];
    }

    #[DataProvider("regionProvider")]
    public function testConstructorWithDifferentRegions(Region $region): void
    {
        $client = new ManagementApiClient(
            personalAccessToken: "test-token",
            region: $region,
        );

        $this->assertInstanceOf(CurlHttpClient::class, $client->httpClient());
    }

    public function testConstructorWithCustomBaseUri(): void
    {
        $customBaseUri = "https://custom.api.example.com";

        $client = new ManagementApiClient(
            personalAccessToken: "test-token",
            region: Region::EU,
            baseUri: $customBaseUri,
        );

        $this->assertInstanceOf(CurlHttpClient::class, $client->httpClient());
    }

    public function testConstructorWithShouldRetryEnabled(): void
    {
        $client = new ManagementApiClient(
            personalAccessToken: "test-token",
            region: Region::EU,
            baseUri: null,
            shouldRetry: true,
        );

        $this->assertInstanceOf(
            RetryableHttpClient::class,
            $client->httpClient(),
        );
    }

    public function testConstructorWithShouldRetryDisabled(): void
    {
        $client = new ManagementApiClient(
            personalAccessToken: "test-token",
            region: Region::EU,
            baseUri: null,
            shouldRetry: false,
        );

        $this->assertInstanceOf(CurlHttpClient::class, $client->httpClient());
    }

    public function testInitStaticMethod(): void
    {
        $client = ManagementApiClient::init(personalAccessToken: "test-token");

        $this->assertInstanceOf(CurlHttpClient::class, $client->httpClient());
    }

    public function testInitStaticMethodWithRegion(): void
    {
        $client = ManagementApiClient::init(
            personalAccessToken: "test-token",
            region: Region::US,
        );

        $this->assertInstanceOf(CurlHttpClient::class, $client->httpClient());
    }

    public function testInitStaticMethodWithCustomBaseUri(): void
    {
        $client = ManagementApiClient::init(
            personalAccessToken: "test-token",
            region: Region::EU,
            baseUri: "https://custom.api.example.com",
        );

        $this->assertInstanceOf(CurlHttpClient::class, $client->httpClient());
    }

    public function testInitTestStaticMethod(): void
    {
        $mockHttpClient = new MockHttpClient();

        $client = ManagementApiClient::initTest($mockHttpClient);

        $this->assertSame($mockHttpClient, $client->httpClient());
    }

    public function testInitTestStaticMethodWithCustomAssetClient(): void
    {
        $mockHttpClient = new MockHttpClient();
        $mockAssetClient = new MockHttpClient();

        $client = ManagementApiClient::initTest(
            $mockHttpClient,
            $mockAssetClient,
        );

        $this->assertSame($mockHttpClient, $client->httpClient());
        $this->assertSame($mockAssetClient, $client->httpAssetClient());
    }

    public function testInitTestStaticMethodUsesDefaultAssetClient(): void
    {
        $mockHttpClient = new MockHttpClient();

        $client = ManagementApiClient::initTest($mockHttpClient);

        $this->assertInstanceOf(
            MockHttpClient::class,
            $client->httpAssetClient(),
        );
    }

    public function testHttpClientAccessor(): void
    {
        $mockHttpClient = new MockHttpClient();
        $client = ManagementApiClient::initTest($mockHttpClient);

        $this->assertSame($mockHttpClient, $client->httpClient());
    }

    public function testHttpAssetClientAccessor(): void
    {
        $mockHttpClient = new MockHttpClient();
        $mockAssetClient = new MockHttpClient();

        $client = ManagementApiClient::initTest(
            $mockHttpClient,
            $mockAssetClient,
        );

        $this->assertSame($mockAssetClient, $client->httpAssetClient());
    }
}
