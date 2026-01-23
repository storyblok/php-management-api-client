<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\MockHttpClient;

final class ManagementApiClientTest extends TestCase
{
    public function testMultipleResourcesStoryblokData(): void
    {
        $responses = [
            $this->mockResponse("list-internal-tags", 200, [
                "total" => 8,
                "per-page" => 25,
            ]),
            $this->mockResponse("empty-tags", 404),
        ];

        $client = new MockHttpClient(
            $responses,
            baseUri: "https://mapi.storyblok.com",
        );
        $mapiClient = ManagementApiClient::initTest($client);
        $managementApi = $mapiClient->managementApi();

        $spaceId = "321388";
        $response = $managementApi->get(
            sprintf("spaces/%s/internal_tags", $spaceId),
            [
                "by_object_type" => "asset",
            ],
        );

        $this->assertSame("https://mapi.storyblok.com/v1/spaces/321388/internal_tags?by_object_type=asset", $response->getLastCalledUrl());
        $this->assertSame(8, $response->total());
        $this->assertTrue($response->isOk());

        $tags = $response->data()->get("internal_tags");
        $this->assertCount(8, $tags);

        foreach ($tags as $tag) {
            $this->assertSame("asset", $tag->get("object_type"));
            $this->assertIsString($tag->get("name"));
            $this->assertIsNumeric($tag->get("id"));
            $this->assertGreaterThan(1000, (int) $tag->get("id"));
        }

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage(
            'HTTP 404 returned for "https://mapi.storyblok.com/v1/spaces/321388/internal_tags?by_object_type=asset&search=somethingnotexists',
        );

        $managementApi->get(sprintf("spaces/%s/internal_tags", $spaceId), [
            "by_object_type" => "asset",
            "search" => "somethingnotexists",
        ]);
    }

    public function testCreateResourceStoryblokData(): void
    {
        $responses = [
            $this->mockResponse("one-internal-tag", 200),
            $this->mockResponse("empty-tags", 404),
        ];

        $client = new MockHttpClient(
            $responses,
            baseUri: "https://mapi.storyblok.com",
        );
        $mapiClient = ManagementApiClient::initTest($client);
        $managementApi = $mapiClient->managementApi();

        $spaceId = "321388";
        $response = $managementApi->post(
            sprintf("spaces/%s/internal_tags", $spaceId),
            [
                "internal_tag" => [
                    "name" => "new tag",
                    "object_type" => "asset",
                ],
            ],
        );

        $this->assertTrue($response->isOk());

        $tag = $response->data()->get("internal_tag");

        $this->assertIsString($tag->get("name"));
        $this->assertIsString($tag->getString("name"));
        $this->assertSame("some", $tag->getString("name"));
    }

    public function testDeleteResourceStoryblokData(): void
    {
        $responses = [
            $this->mockResponse("one-internal-tag", 200),
            $this->mockResponse("empty-tags", 404),
        ];

        $client = new MockHttpClient(
            $responses,
            baseUri: "https://mapi.storyblok.com",
        );
        $mapiClient = ManagementApiClient::initTest($client);
        $managementApi = $mapiClient->managementApi();

        $spaceId = "321388";
        $tagId = "56980";

        $response = $managementApi->delete(
            sprintf("spaces/%s/internal_tags/%s", $spaceId, $tagId),
        );

        $this->assertTrue($response->isOk());

        $tag = $response->data()->get("internal_tag");

        $this->assertIsString($tag->get("name"));
        $this->assertIsString($tag->getString("name"));
        $this->assertSame("some", $tag->getString("name"));
    }

    public function testEditResourceStoryblokData(): void
    {
        $responses = [
            $this->mockResponse("one-internal-tag", 200),
            $this->mockResponse("empty-tags", 404),
        ];

        $client = new MockHttpClient(
            $responses,
            baseUri: "https://mapi.storyblok.com",
        );
        $mapiClient = ManagementApiClient::initTest($client);
        $managementApi = $mapiClient->managementApi();

        $spaceId = "321388";
        $tagId = "56980";

        $response = $managementApi->put(
            sprintf("spaces/%s/internal_tags/%s", $spaceId, $tagId),
        );

        $this->assertTrue($response->isOk());

        $tag = $response->data()->get("internal_tag");

        $this->assertIsString($tag->get("name"));
        $this->assertIsString($tag->getString("name"));
        $this->assertSame("some", $tag->getString("name"));
    }

    public function testStoryblokDataToArray(): void
    {
        $responses = [
            $this->mockResponse("list-internal-tags", 200, [
                "total" => 8,
                "per-page" => 25,
            ]),
            $this->mockResponse("empty-tags", 404),
        ];

        $client = new MockHttpClient(
            $responses,
            baseUri: "https://mapi.storyblok.com",
        );
        $mapiClient = ManagementApiClient::initTest($client);
        $managementApi = $mapiClient->managementApi();

        $spaceId = "321388";
        $response = $managementApi->get(
            sprintf("spaces/%s/internal_tags", $spaceId),
            [
                "by_object_type" => "asset",
            ],
        );

        $this->assertIsArray($response->toArray());
    }
}
