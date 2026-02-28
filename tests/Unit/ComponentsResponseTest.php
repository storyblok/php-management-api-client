<?php

declare(strict_types=1);

namespace Tests\Unit;

use Storyblok\ManagementApi\Data\Component;
use Storyblok\ManagementApi\Endpoints\ComponentApi;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;
use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Tests\TestCase;

final class ComponentsResponseTest extends TestCase
{
    public function testDataReturnsComponentsCollection(): void
    {
        $responses = [$this->mockResponse("list-components", 200)];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $componentApi = new ComponentApi($mapiClient, "2222");

        $response = $componentApi->all();
        $components = $response->data();

        $this->assertSame(5, $components->howManyComponents());
    }

    public function testDataThrowsExceptionOnMissingKey(): void
    {
        $response = $this->mockResponse("empty-story", 200);

        $client = new MockHttpClient([$response]);
        $mapiClient = ManagementApiClient::initTest($client);
        $componentApi = new ComponentApi($mapiClient, "2222");

        $componentsResponse = $componentApi->all();

        $this->expectException(StoryblokFormatException::class);
        $this->expectExceptionMessage("Expected 'components' in the response.");

        $componentsResponse->data();
    }

    public function testDataFoldersReturnsComponentFolders(): void
    {
        $responses = [$this->mockResponse("list-components", 200)];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $componentApi = new ComponentApi($mapiClient, "2222");

        $response = $componentApi->all();
        $folders = $response->dataFolders();

        $this->assertSame(0, $folders->howManyComponentFolders());
    }

    public function testDataFoldersThrowsExceptionOnMissingKey(): void
    {
        $response = $this->mockResponse("empty-story", 200);

        $client = new MockHttpClient([$response]);
        $mapiClient = ManagementApiClient::initTest($client);
        $componentApi = new ComponentApi($mapiClient, "2222");

        $componentsResponse = $componentApi->all();

        $this->expectException(StoryblokFormatException::class);
        $this->expectExceptionMessage("Expected 'component_groups' in the response.");

        $componentsResponse->dataFolders();
    }

    public function testIteratingOverComponents(): void
    {
        $responses = [$this->mockResponse("list-components", 200)];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $componentApi = new ComponentApi($mapiClient, "2222");

        $components = $componentApi->all()->data();

        $names = [];
        foreach ($components as $component) {
            $this->assertInstanceOf(Component::class, $component);
            $names[] = $component->name();
        }

        $this->assertSame(
            ["article-page", "feature", "grid", "page", "teaser"],
            $names,
        );
    }

    public function testFirstComponentAttributes(): void
    {
        $responses = [$this->mockResponse("list-components", 200)];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $componentApi = new ComponentApi($mapiClient, "2222");

        $components = $componentApi->all()->data();

        /** @var Component $first */
        $first = $components->current();

        $this->assertSame("article-page", $first->name());
        $this->assertSame("6842056", $first->id());
        $this->assertSame("article-page", $first->realName());
        $this->assertTrue($first->isRoot());
        $this->assertSame("2025-01-23T09:54:25.516Z", $first->createdAt());
        $this->assertSame("2025-02-21T21:19:13.157Z", $first->updatedAt());
    }

    public function testRootAndNestableComponents(): void
    {
        $responses = [$this->mockResponse("list-components", 200)];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $componentApi = new ComponentApi($mapiClient, "2222");

        $components = $componentApi->all()->data();

        $rootNames = [];
        $nestableNames = [];
        /** @var Component $component */
        foreach ($components as $component) {
            if ($component->isRoot()) {
                $rootNames[] = $component->name();
            }

            if ($component->getBoolean("is_nestable")) {
                $nestableNames[] = $component->name();
            }
        }

        $this->assertSame(["article-page", "page"], $rootNames);
        $this->assertSame(["feature", "grid", "teaser"], $nestableNames);
    }
}
