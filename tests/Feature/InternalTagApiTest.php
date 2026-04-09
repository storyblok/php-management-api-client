<?php

declare(strict_types=1);

namespace Tests\Feature;

use Storyblok\ManagementApi\Data\InternalTag;
use Storyblok\ManagementApi\Endpoints\InternalTagApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Storyblok\ManagementApi\QueryParameters\InternalTagsParams;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\MockHttpClient;
use Tests\TestCase;

final class InternalTagApiTest extends TestCase
{
    public function testListInternalTags(): void
    {
        $responses = [
            $this->mockResponse("list-internal-tags", 200, [
                "total" => 8,
                "per-page" => 25,
            ]),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $internalTagApi = new InternalTagApi($mapiClient, "222");

        $response = $internalTagApi->page();
        $data = $response->data();

        $this->assertSame(200, $response->getResponseStatusCode());
        $this->assertCount(8, $data);
        $this->assertSame(8, $response->total());

        $this->assertContainsOnlyInstancesOf(InternalTag::class, $data);
    }

    public function testListInternalTagsUrl(): void
    {
        $responses = [
            $this->mockResponse("list-internal-tags", 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $internalTagApi = new InternalTagApi($mapiClient, "222");

        $response = $internalTagApi->page();

        $this->assertSame(
            "https://example.com/v1/spaces/222/internal_tags",
            $response->getLastCalledUrl(),
        );
    }

    public function testListInternalTagsWithParams(): void
    {
        $responses = [
            $this->mockResponse("list-internal-tags", 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $internalTagApi = new InternalTagApi($mapiClient, "222");

        $response = $internalTagApi->page(
            new InternalTagsParams(byObjectType: "asset", search: "some"),
        );

        $url = $response->getLastCalledUrl();
        $this->assertMatchesRegularExpression('/.*by_object_type=asset.*$/', $url);
        $this->assertMatchesRegularExpression('/.*search=some.*$/', $url);
    }

    public function testGetOneInternalTag(): void
    {
        $responses = [
            $this->mockResponse("one-internal-tag", 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $internalTagApi = new InternalTagApi($mapiClient, "222");

        $response = $internalTagApi->get("56932");
        $data = $response->data();

        $this->assertSame(200, $response->getResponseStatusCode());
        $this->assertSame("56932", $data->id());
        $this->assertSame("some", $data->name());
        $this->assertSame("asset", $data->objectType());
        $this->assertSame(
            "https://example.com/v1/spaces/222/internal_tags/56932",
            $response->getLastCalledUrl(),
        );
    }

    public function testGetOneInternalTagNotFound(): void
    {
        $responses = [
            $this->mockResponse("one-internal-tag", 404),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $internalTagApi = new InternalTagApi($mapiClient, "222");

        $this->expectException(ClientException::class);
        $internalTagApi->get("999");
    }

    public function testCreateInternalTag(): void
    {
        $responses = [
            $this->mockResponse("one-internal-tag", 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $internalTagApi = new InternalTagApi($mapiClient, "222");

        $tag = new InternalTag("some");
        $tag->set("object_type", "asset");

        $response = $internalTagApi->create($tag);
        $data = $response->data();

        $this->assertSame(200, $response->getResponseStatusCode());
        $this->assertSame("some", $data->name());
        $this->assertSame(
            "https://example.com/v1/spaces/222/internal_tags",
            $response->getLastCalledUrl(),
        );
    }

    public function testUpdateInternalTag(): void
    {
        $responses = [
            $this->mockResponse("one-internal-tag", 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $internalTagApi = new InternalTagApi($mapiClient, "222");

        $tag = new InternalTag("renamed");
        $tag->set("object_type", "asset");

        $response = $internalTagApi->update("56932", $tag);

        $this->assertSame(200, $response->getResponseStatusCode());
        $this->assertSame(
            "https://example.com/v1/spaces/222/internal_tags/56932",
            $response->getLastCalledUrl(),
        );
    }

    public function testDeleteInternalTag(): void
    {
        $responses = [
            $this->mockResponse("one-internal-tag", 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $internalTagApi = new InternalTagApi($mapiClient, "222");

        $response = $internalTagApi->delete("56932");

        $this->assertSame(200, $response->getResponseStatusCode());
        $this->assertSame(
            "https://example.com/v1/spaces/222/internal_tags/56932",
            $response->getLastCalledUrl(),
        );
    }

    public function testInternalTagForDifferentSpace(): void
    {
        $responses = [
            $this->mockResponse("list-internal-tags", 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $internalTagApi = new InternalTagApi($mapiClient, "999");

        $response = $internalTagApi->page();

        $this->assertSame(
            "https://example.com/v1/spaces/999/internal_tags",
            $response->getLastCalledUrl(),
        );
    }
}
