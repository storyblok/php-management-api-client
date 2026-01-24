<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Storyblok\ManagementApi\Data\Component;
use Storyblok\ManagementApi\Endpoints\ComponentApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Storyblok\ManagementApi\QueryParameters\ComponentsParams;
use Symfony\Component\HttpClient\MockHttpClient;

final class ComponentApiTest extends TestCase
{
    public function testListOfComponentsParams(): void
    {
        $responses = [
            $this->mockResponse("list-components", 200),
            $this->mockResponse("list-components", 200),
            $this->mockResponse("list-components", 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $componentApi = new ComponentApi($mapiClient, "2222");

        $storyblokResponse = $componentApi->all();
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression(
            '/.*\/v1\/spaces\/2222\/components$/',
            $url,
        );

        $storyblokResponse = $componentApi->all(
            new ComponentsParams(byIds: "1234567890"),
        );
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression(
            '/.*\/v1\/spaces\/2222\/components\?by_ids=1234567890$/',
            $url,
        );

        $storyblokResponse = $componentApi->all(
            new ComponentsParams(byIds: "1234567890", isRoot: true),
        );
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression(
            '/.*\/v1\/spaces\/2222\/components\?is_root=1&by_ids=1234567890$/',
            $url,
        );
    }

    public function testGettingOneComponent(): void
    {
        $responses = [$this->mockResponse("one-component", 200)];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $componentApi = new ComponentApi($mapiClient, "2222");

        $componentResponse = $componentApi->get("7223149");
        $url = $componentResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression(
            '/.*\/v1\/spaces\/2222\/components\/7223149$/',
            $url,
        );

        $component = $componentResponse->data();

        $this->assertSame(7223149, $component->getInt("id"));
        $this->assertSame("7223149", $component->id());
        $this->assertSame("text-section", $component->name());
        $this->assertSame("2025-04-15T21:32:55.495Z", $component->createdAt());
        $this->assertSame("2025-04-15T21:32:55.495Z", $component->updatedAt());
        $this->assertFalse($component->isRoot());
    }
}
