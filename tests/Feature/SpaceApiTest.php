<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Storyblok\ManagementApi\Data\Space;
use Storyblok\ManagementApi\Data\Spaces;
use Storyblok\ManagementApi\Endpoints\SpaceApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\MockHttpClient;

final class SpaceApiTest extends TestCase
{
    public function testMakingSpace(): void
    {
        $spaceData = Space::make([]);
        $this->assertInstanceOf(Space::class, $spaceData);

        $spacesData = Spaces::make([]);
        $this->assertInstanceOf(Spaces::class, $spacesData);

        $responses = [
            $this->mockResponse("one-space", 200),
            $this->mockResponse("empty-space", 404),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $spaceApi = new SpaceApi($mapiClient);

        $this->assertInstanceOf(SpaceApi::class, $spaceApi);
    }

    public function testOneSpaceSpace(): void
    {
        $responses = [
            $this->mockResponse("one-space", 200),
            $this->mockResponse("empty-space", 404),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $spaceApi = new SpaceApi($mapiClient);

        $storyblokResponse = $spaceApi->get("111");
        $storyblokData = $storyblokResponse->data();

        $this->assertSame("Example Space", $storyblokData->get("name"));
        $this->assertSame("Example Space", $storyblokData->name());
        $this->assertSame("1114", $storyblokData->ownerId());
        $this->assertSame("680", $storyblokData->id());
        $this->assertSame("2018-11-10", $storyblokData->createdAt());
        $this->assertSame("2018-11-11", $storyblokData->updatedAt());
        $this->assertSame("Starter (Trial)", $storyblokData->planDescription());
        $this->assertSame(
            "https://example.storyblok.com",
            $storyblokData->domain(),
        );
        $this->assertSame(
            "8IE7MzYCzw5d7KLckDa38Att",
            $storyblokData->firstToken(),
        );
        $this->assertFalse($storyblokData->isDemo());
        $this->assertSame(200, $storyblokResponse->getResponseStatusCode());
        $this->assertCount(0, $storyblokData->environments());

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage(
            'HTTP 404 returned for "https://example.com/v1/spaces/111notexists',
        );

        $spaceApi->get("111notexists");
    }

    public function testOneSpaceSetters(): void
    {
        $responses = [$this->mockResponse("one-space", 200)];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $spaceApi = new SpaceApi($mapiClient);

        $storyblokResponse = $spaceApi->get("111");
        $storyblokData = $storyblokResponse->data();

        $storyblokData->setName("New Name");
        $this->assertSame("New Name", $storyblokData->get("name"));
        $this->assertSame("2018-11-10", $storyblokData->createdAt());
        $this->assertSame(
            "https://example.storyblok.com",
            $storyblokData->get("domain"),
        );

        $storyblokData->setDomain("example.com");
        $this->assertSame("New Name", $storyblokData->get("name"));
        $this->assertSame("2018-11-10", $storyblokData->createdAt());
        $this->assertSame("example.com", $storyblokData->get("domain"));
    }

    public function testCreatingOneSpaceSpace(): void
    {
        $responses = [
            $this->mockResponse("one-space", 200),
            $this->mockResponse("empty-space", 404),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $spaceApi = new SpaceApi($mapiClient);

        $spaceData = new Space("New Name");
        $spaceData->setDomain("https://example.storyblok.com");

        $this->assertSame("New Name", $spaceData->get("name"));
        $this->assertSame("", $spaceData->createdAt());
        $this->assertSame(
            "https://example.storyblok.com",
            $spaceData->get("domain"),
        );

        $storyblokResponse = $spaceApi->create($spaceData);
        $storyblokData = $storyblokResponse->data();

        $this->assertSame("Example Space", $storyblokData->get("name"));
        $this->assertSame("Example Space", $storyblokData->name());
        $this->assertSame("2018-11-10", $storyblokData->createdAt());
        $this->assertSame("Starter (Trial)", $storyblokData->planDescription());
        $this->assertSame(200, $storyblokResponse->getResponseStatusCode());
    }

    public function testThrowsExceptionInCreatingSpace(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage(
            'HTTP 404 returned for "https://example.com/v1/spaces',
        );

        $responses = [$this->mockResponse("empty-space", 404)];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $spaceApi = new SpaceApi($mapiClient);

        $spaceApi->create(new Space(""));
    }

    public function testBackupOneSpaceSpace(): void
    {
        $responses = [
            $this->mockResponse("one-space", 200),
            $this->mockResponse("empty-space", 404),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $spaceApi = new SpaceApi($mapiClient);

        $storyblokResponse = $spaceApi->backup("111");
        $storyblokData = $storyblokResponse->data();

        $this->assertSame("Example Space", $storyblokData->get("name"));
        $this->assertSame("Example Space", $storyblokData->name());
        $this->assertSame("2018-11-10", $storyblokData->createdAt());
        $this->assertSame("Starter (Trial)", $storyblokData->planDescription());
        $this->assertSame(200, $storyblokResponse->getResponseStatusCode());
    }

    public function testDeleteOneSpaceSpace(): void
    {
        $responses = [
            $this->mockResponse("one-space", 200),
            $this->mockResponse("empty-space", 404),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $spaceApi = new SpaceApi($mapiClient);

        $storyblokResponse = $spaceApi->delete("111");
        $storyblokData = $storyblokResponse->data();

        $this->assertSame("Example Space", $storyblokData->get("name"));
        $this->assertSame("Example Space", $storyblokData->name());
        $this->assertSame("2018-11-10", $storyblokData->createdAt());
        $this->assertSame("Starter (Trial)", $storyblokData->planDescription());
        $this->assertSame(200, $storyblokResponse->getResponseStatusCode());
    }

    public function testDuplicatingOneSpaceSpace(): void
    {
        $responses = [
            $this->mockResponse("one-space", 200),
            $this->mockResponse("one-space", 200),
            $this->mockResponse("empty-space", 404),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $spaceApi = new SpaceApi($mapiClient);

        $storyblokResponse = $spaceApi->duplicate("111", "New Space Name");
        $storyblokData = $storyblokResponse->data();

        $this->assertSame("Example Space", $storyblokData->get("name"));
        $this->assertSame("Example Space", $storyblokData->name());
        $this->assertSame("2018-11-10", $storyblokData->createdAt());
        $this->assertSame("Starter (Trial)", $storyblokData->planDescription());
        $this->assertSame(200, $storyblokResponse->getResponseStatusCode());

        $storyblokResponse = $spaceApi->duplicate(
            "111",
            "New Space Name",
            inOrg: true,
        );
        $storyblokData = $storyblokResponse->data();

        $this->assertSame("Example Space", $storyblokData->get("name"));

        $this->assertSame("Example Space", $storyblokData->name());
        $this->assertSame("2018-11-10", $storyblokData->createdAt());
        $this->assertSame("Starter (Trial)", $storyblokData->planDescription());
        $this->assertSame(200, $storyblokResponse->getResponseStatusCode());
    }

    public function testMultipleSpacesSpace(): void
    {
        $responses = [
            $this->mockResponse("list-spaces", 200),
            $this->mockResponse("empty-space", 404),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $spaceApi = new SpaceApi($mapiClient);

        $storyblokResponse = $spaceApi->all();
        $storyblokData = $storyblokResponse->data();

        $this->assertSame("Example Space", $storyblokData->get("0.name"));
        $this->assertSame(200, $storyblokResponse->getResponseStatusCode());

        foreach ($storyblokData as $spaceItem) {
            $this->assertIsString($spaceItem->name());
            $this->assertSame("1114", $spaceItem->ownerId());
        }

        $this->assertSame(2, $storyblokData->howManySpaces());
    }

    public function testUpdateSpace(): void
    {
        $responses = [$this->mockResponse("one-space", 200)];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $spaceApi = new SpaceApi($mapiClient);

        $spaceData = new Space("New Name");
        $spaceData->setDomain("https://example.storyblok.com");

        $storyblokResponse = $spaceApi->update("111", $spaceData);
        $storyblokData = $storyblokResponse->data();

        $this->assertSame("Example Space", $storyblokData->get("name"));
        $this->assertSame("Example Space", $storyblokData->name());
        $this->assertSame("2018-11-10", $storyblokData->createdAt());
        $this->assertSame("Starter (Trial)", $storyblokData->planDescription());
        $this->assertSame(200, $storyblokResponse->getResponseStatusCode());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Space ID cannot be empty");
        $spaceData = new Space("");
        $spaceApi->update("", $spaceData);
    }
}
