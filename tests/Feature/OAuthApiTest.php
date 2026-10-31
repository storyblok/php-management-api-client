<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Storyblok\ManagementApi\Data\Enum\Region;
use Storyblok\ManagementApi\Data\OAuthUserRole;
use Storyblok\ManagementApi\Endpoints\OAuthApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\MockHttpClient;
use Tests\TestCase;

final class OAuthApiTest extends TestCase
{
    public function testUserInfoRetrievesOAuthUserInfo(): void
    {
        $client = new MockHttpClient([
            $this->mockResponse("oauth-user-info", 200),
        ]);
        $managementClient = ManagementApiClient::initTest($client);
        $oauthApi = new OAuthApi($managementClient);

        $response = $oauthApi->userInfo();

        $this->assertTrue($response->isOk());
        $this->assertSame(
            "https://api.storyblok.com/oauth/user_info",
            $response->getLastCalledUrl(),
        );

        $data = $response->data();
        $this->assertSame("My name", $data->user()->friendlyName());
        $this->assertSame("20", $data->user()->id());
        $this->assertSame(
            [
                ["name" => "admin"],
                ["name" => "editor"],
            ],
            $data->roles()->toArray(),
        );

        $firstRole = $data->roles()[0];
        $this->assertInstanceOf(OAuthUserRole::class, $firstRole);
        $this->assertSame("admin", $firstRole->name());

        $roleNames = [];
        foreach ($data->roles() as $role) {
            $this->assertInstanceOf(OAuthUserRole::class, $role);
            $roleNames[] = $role->name();
        }

        $this->assertSame(["admin", "editor"], $roleNames);
    }

    /**
     * @return \Iterator<string, array{Region, string}>
     */
    public static function regionProvider(): \Iterator
    {
        yield "EU" => [Region::EU, "https://api.storyblok.com/oauth/user_info"];
        yield "US" => [Region::US, "https://api-us.storyblok.com/oauth/user_info"];
        yield "CA" => [Region::CA, "https://api-ca.storyblok.com/oauth/user_info"];
        yield "AP" => [Region::AP, "https://api-ap.storyblok.com/oauth/user_info"];
        yield "CN" => [Region::CN, "https://app.storyblokchina.cn/oauth/user_info"];
    }

    #[DataProvider('regionProvider')]
    public function testUserInfoUsesRegionalOAuthHost(Region $region, string $expectedUrl): void
    {
        $client = new MockHttpClient([
            $this->mockResponse("oauth-user-info", 200),
        ]);
        $managementClient = ManagementApiClient::initTest($client, null, $region);

        $response = (new OAuthApi($managementClient))->userInfo();

        $this->assertSame($expectedUrl, $response->getLastCalledUrl());
    }

    public function testUserInfoNonSuccessfulResponseThrowsWhenDecoded(): void
    {
        $client = new MockHttpClient([
            $this->mockResponse("error", 401),
        ]);
        $managementClient = ManagementApiClient::initTest($client);
        $oauthApi = new OAuthApi($managementClient);

        $response = $oauthApi->userInfo();

        $this->assertFalse($response->isOk());
        $this->assertSame(401, $response->getResponseStatusCode());
        $this->assertSame("401 - Unauthorized. No valid API key provided.", $response->getErrorMessage());

        $this->expectException(ClientException::class);

        $response->data();
    }
}
