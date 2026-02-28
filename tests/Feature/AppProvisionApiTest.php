<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Storyblok\ManagementApi\Data\AppProvision;
use Storyblok\ManagementApi\Endpoints\AppProvisionApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class AppProvisionApiTest extends TestCase
{
    public function testInstallApp(): void
    {
        $responses = [
            $this->mockResponse('one-app-provision', 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $appProvisionApi = new AppProvisionApi($mapiClient, '290849829374737');

        $storyblokResponse = $appProvisionApi->install(14);
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression('/.*app_provisions.*$/', $url);
        $this->assertSame(200, $storyblokResponse->getResponseStatusCode());

        $data = $storyblokResponse->data();
        $this->assertSame('Activities', $data->name());
        $this->assertSame('14', $data->appId());
        $this->assertSame('activity', $data->slug());
        $this->assertSame(1100, $data->planLevel());
        $this->assertFalse($data->inSidebar());
        $this->assertFalse($data->inToolbar());
        $this->assertFalse($data->enableSpaceSettings());
    }

    public function testPageOfAppProvisions(): void
    {
        $responses = [
            $this->mockResponse('list-app-provisions', 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $appProvisionApi = new AppProvisionApi($mapiClient, '290849829374737');

        $storyblokResponse = $appProvisionApi->page();
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression('/.*app_provisions.*$/', $url);
        $this->assertSame(200, $storyblokResponse->getResponseStatusCode());

        $data = $storyblokResponse->data();
        $this->assertSame(1, $data->howManyAppProvisions());
        $this->assertSame("Activities", $data->get("0.name"));
    }

    public function testAppProvisionsIteration(): void
    {
        $responses = [
            $this->mockResponse('list-app-provisions', 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $appProvisionApi = new AppProvisionApi($mapiClient, '290849829374737');

        $storyblokResponse = $appProvisionApi->page();
        $data = $storyblokResponse->data();

        foreach ($data as $appProvision) {
            $this->assertInstanceOf(AppProvision::class, $appProvision);
            $this->assertIsString($appProvision->name());
            $this->assertIsString($appProvision->slug());
        }
    }

    public function testGetAppProvision(): void
    {
        $responses = [
            $this->mockResponse('one-app-provision-detail', 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $appProvisionApi = new AppProvisionApi($mapiClient, '290849829374737');

        $storyblokResponse = $appProvisionApi->get(14);
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression('/.*app_provisions\/14.*$/', $url);
        $this->assertSame(200, $storyblokResponse->getResponseStatusCode());

        $data = $storyblokResponse->data();
        $this->assertSame('Activities', $data->name());
        $this->assertSame('14', $data->appId());
        $this->assertSame('activity', $data->slug());
        $this->assertSame(1100, $data->planLevel());
        $this->assertFalse($data->inSidebar());
        $this->assertFalse($data->inToolbar());
        $this->assertFalse($data->enableSpaceSettings());
    }

    public function testDeleteAppProvision(): void
    {
        $responses = [
            new MockResponse('', ['http_code' => 204]),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $appProvisionApi = new AppProvisionApi($mapiClient, '290849829374737');

        $storyblokResponse = $appProvisionApi->delete(14);
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression('/.*app_provisions\/14.*$/', $url);
        $this->assertSame(204, $storyblokResponse->getResponseStatusCode());
        $this->assertTrue($storyblokResponse->isOk());
    }
}
