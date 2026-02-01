<?php

declare(strict_types=1);

namespace Tests\Feature;

use InvalidArgumentException;
use Storyblok\ManagementApi\Data\Component;
use Storyblok\ManagementApi\Endpoints\ComponentApi;
use Storyblok\ManagementApi\Exceptions\InvalidStoryDataException;
use Storyblok\ManagementApi\ManagementApiClient;
use Storyblok\ManagementApi\QueryParameters\ComponentsParams;
use Symfony\Component\HttpClient\MockHttpClient;
use Tests\TestCase;

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

    public function testCreateComponent(): void
    {
        $responses = [$this->mockResponse("one-component", 200)];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $componentApi = new ComponentApi($mapiClient, "2222");

        $componentData = new Component("new-component");
        $componentData->setDisplayName("New Component");
        $componentData->setRoot(true);

        $componentResponse = $componentApi->create($componentData);
        $url = $componentResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression(
            '/.*\/v1\/spaces\/2222\/components$/',
            $url,
        );
        $this->assertTrue($componentResponse->isOk());
    }

    public function testUpdateComponent(): void
    {
        $responses = [$this->mockResponse("one-component", 200)];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $componentApi = new ComponentApi($mapiClient, "2222");

        $componentData = new Component("text-section");
        $componentData->setDisplayName("Updated Text Section");

        $componentResponse = $componentApi->update("7223149", $componentData);
        $url = $componentResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression(
            '/.*\/v1\/spaces\/2222\/components\/7223149$/',
            $url,
        );
        $this->assertTrue($componentResponse->isOk());
    }

    public function testGetComponentWithEmptyIdThrowsException(): void
    {
        $client = new MockHttpClient([]);
        $mapiClient = ManagementApiClient::initTest($client);
        $componentApi = new ComponentApi($mapiClient, "2222");

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Component ID cannot be empty");

        $componentApi->get("");
    }

    public function testGetComponentWithZeroIdThrowsException(): void
    {
        $client = new MockHttpClient([]);
        $mapiClient = ManagementApiClient::initTest($client);
        $componentApi = new ComponentApi($mapiClient, "2222");

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Component ID cannot be empty");

        $componentApi->get("0");
    }

    public function testUpdateComponentWithEmptyIdThrowsException(): void
    {
        $client = new MockHttpClient([]);
        $mapiClient = ManagementApiClient::initTest($client);
        $componentApi = new ComponentApi($mapiClient, "2222");

        $componentData = new Component("test-component");

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Component ID cannot be empty");

        $componentApi->update("", $componentData);
    }

    public function testCreateComponentWithInvalidDataThrowsException(): void
    {
        $client = new MockHttpClient([]);
        $mapiClient = ManagementApiClient::initTest($client);
        $componentApi = new ComponentApi($mapiClient, "2222");

        $componentData = new Component("valid-name");
        // Remove the name to make it invalid by setting empty data
        $componentData->setData([]);

        $this->expectException(InvalidStoryDataException::class);
        $this->expectExceptionMessage("Invalid component data provided");

        $componentApi->create($componentData);
    }
}
