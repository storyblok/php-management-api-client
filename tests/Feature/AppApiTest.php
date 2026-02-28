<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Storyblok\ManagementApi\Data\App;
use Storyblok\ManagementApi\Endpoints\AppApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Storyblok\ManagementApi\QueryParameters\AppsParams;
use Symfony\Component\HttpClient\MockHttpClient;

final class AppApiTest extends TestCase
{
    public function testPageOfApps(): void
    {
        $responses = [
            $this->mockResponse('list-apps', 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $appApi = new AppApi($mapiClient);

        $params = new AppsParams(spaceId: 12345);
        $storyblokResponse = $appApi->page($params);
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression('/.*apps.*$/', $url);
        $this->assertSame(200, $storyblokResponse->getResponseStatusCode());

        $data = $storyblokResponse->data();
        $this->assertSame(3, $data->howManyApps());
        $this->assertSame("SEO App", $data->get("0.name"));
    }

    public function testAppsIteration(): void
    {
        $responses = [
            $this->mockResponse('list-apps', 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $appApi = new AppApi($mapiClient);

        $params = new AppsParams(spaceId: 12345, page: 1, perPage: 25);
        $storyblokResponse = $appApi->page($params);
        $data = $storyblokResponse->data();

        foreach ($data as $app) {
            $this->assertInstanceOf(App::class, $app);
            $this->assertIsString($app->name());
            $this->assertIsString($app->slug());
        }
    }

    public function testOneApp(): void
    {
        $responses = [
            $this->mockResponse('one-app', 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $appApi = new AppApi($mapiClient);

        $storyblokResponse = $appApi->get(14, 290849829374737);
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression('/.*apps\/14.*$/', $url);
        $this->assertSame(200, $storyblokResponse->getResponseStatusCode());

        $data = $storyblokResponse->data();
        $this->assertSame('14', $data->id());
        $this->assertSame('Activities', $data->name());
        $this->assertSame('activity', $data->slug());
        $this->assertSame('approved', $data->status());
        $this->assertSame('Storyblok GmbH', $data->author());
        $this->assertSame('https://www.storyblok.com', $data->website());
        $this->assertSame('Track changes of all space activities.', $data->intro());
        $this->assertSame(1100, $data->planLevel());
        $this->assertFalse($data->inSidebar());
        $this->assertFalse($data->inToolbar());
    }
}
