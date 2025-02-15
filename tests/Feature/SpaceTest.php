<?php

declare(strict_types=1);

use Storyblok\ManagementApi\Data\SpaceData;
use Storyblok\ManagementApi\Endpoints\SpaceApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\MockHttpClient;

test("Testing making space", function (): void {
    $spaceData = \Storyblok\ManagementApi\Data\SpaceData::make([]);
    expect($spaceData)->toBeInstanceOf(\Storyblok\ManagementApi\Data\SpaceData::class);
    $spacesData = \Storyblok\ManagementApi\Data\SpacesData::make([]);
    expect($spacesData)->toBeInstanceOf(\Storyblok\ManagementApi\Data\SpacesData::class);
    $responses = [
        \mockResponse("one-space", 200),
        \mockResponse("empty-space", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $spaceApi = new SpaceApi($mapiClient);
    expect($spaceApi)->toBeInstanceOf(SpaceApi::class);

});
test('Testing One space, SpaceData', function (): void {
    $responses = [
        \mockResponse("one-space", 200),
        \mockResponse("empty-space", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $spaceApi = new SpaceApi($mapiClient);

    $storyblokResponse = $spaceApi->get("111");
    $storyblokData = $storyblokResponse->data();
    expect($storyblokData->get("name"))
        ->toBe("Example Space")
        ->and($storyblokData->name())->toBe("Example Space")
        ->and($storyblokData->id())->toBe("680")
        ->and($storyblokData->createdAt())->toBe("2018-11-10")
        ->and($storyblokData->planDescription())->toBe("Starter (Trial)")
    ->and($storyblokResponse->getResponseStatusCode())->toBe(200);

    expect(function () use ($spaceApi): void {
        $storyblokResponse = $spaceApi->get("111notexists");
    })->toThrow(
        \Symfony\Component\HttpClient\Exception\ClientException::class,
        'HTTP 404 returned for "https://example.com/v1/spaces/111notexists'
    );

    //expect($storyblokResponse->getResponseStatusCode())->toBe(404) ;
    //expect($storyblokResponse->asJson())->toBe('["This record could not be found"]');

    $storyblokData->setName("New Name");
    expect($storyblokData->get("name"))
        ->toBe("New Name")
        ->and($storyblokData->createdAt())->toBe("2018-11-10")
        ->and($storyblokData->get("domain"))->toBe("https://example.storyblok.com");

    $storyblokData->setDomain("example.com");
    expect($storyblokData->get("name"))
        ->toBe("New Name")
        ->and($storyblokData->createdAt())->toBe("2018-11-10")
        ->and($storyblokData->get("domain"))->toBe("example.com");
});

test('Testing Creating One space, SpaceData', function (): void {
    $responses = [
        \mockResponse("one-space", 200),
        \mockResponse("empty-space", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $spaceApi = new SpaceApi($mapiClient);

    $spaceData = new SpaceData();

    $spaceData->setName("New Name");
    $spaceData->setDomain("https://example.storyblok.com");

    expect($spaceData->get("name"))
        ->toBe("New Name")
        ->and($spaceData->createdAt())->toBe("")
        ->and($spaceData->get("domain"))->toBe("https://example.storyblok.com");

    $storyblokResponse = $spaceApi->create(
        $spaceData
    );
    $storyblokData = $storyblokResponse->data();
    expect($storyblokData->get("name"))
        ->toBe("Example Space")
        ->and($storyblokData->name())->toBe("Example Space")
        ->and($storyblokData->createdAt())->toBe("2018-11-10")
        ->and($storyblokData->planDescription())->toBe("Starter (Trial)")
        ->and($storyblokResponse->getResponseStatusCode())->toBe(200);

});
test('throws exception in creating space', function (): void {
    $responses = [
        \mockResponse("empty-space", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $spaceApi = new SpaceApi($mapiClient);
    $storyblokResponse = $spaceApi->create(
        new SpaceData([])
    );
})->throws(ClientException::class, 'HTTP 404 returned for "https://example.com/v1/spaces');

test('Testing backup One space, SpaceData', function (): void {
    $responses = [
        \mockResponse("one-space", 200),
        \mockResponse("empty-space", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $spaceApi = new SpaceApi($mapiClient);

    $storyblokResponse = $spaceApi->backup(
        "111"
    );
    $storyblokData = $storyblokResponse->data();
    expect($storyblokData->get("name"))
        ->toBe("Example Space")
        ->and($storyblokData->name())->toBe("Example Space")
        ->and($storyblokData->createdAt())->toBe("2018-11-10")
        ->and($storyblokData->planDescription())->toBe("Starter (Trial)")
        ->and($storyblokResponse->getResponseStatusCode())->toBe(200);

});

test('Testing delete One space, SpaceData', function (): void {
    $responses = [
        \mockResponse("one-space", 200),
        \mockResponse("empty-space", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $spaceApi = new SpaceApi($mapiClient);

    $storyblokResponse = $spaceApi->delete(
        "111"
    );
    $storyblokData = $storyblokResponse->data();
    expect($storyblokData->get("name"))
        ->toBe("Example Space")
        ->and($storyblokData->name())->toBe("Example Space")
        ->and($storyblokData->createdAt())->toBe("2018-11-10")
        ->and($storyblokData->planDescription())->toBe("Starter (Trial)")
        ->and($storyblokResponse->getResponseStatusCode())->toBe(200);

});

test('Testing duplicating One space, SpaceData', function (): void {
    $responses = [
        \mockResponse("one-space", 200),
        \mockResponse("empty-space", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $spaceApi = new SpaceApi($mapiClient);

    $storyblokResponse = $spaceApi->duplicate(
        "111",
        "New Space Name"
    );
    $storyblokData = $storyblokResponse->data();
    expect($storyblokData->get("name"))
        ->toBe("Example Space")
        ->and($storyblokData->name())->toBe("Example Space")
        ->and($storyblokData->createdAt())->toBe("2018-11-10")
        ->and($storyblokData->planDescription())->toBe("Starter (Trial)")
        ->and($storyblokResponse->getResponseStatusCode())->toBe(200);

});

test('Testing multiple spaces, SpaceData', function (): void {
    $responses = [
        \mockResponse("list-spaces", 200),
        \mockResponse("empty-space", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $spaceApi = new SpaceApi($mapiClient);

    $storyblokResponse = $spaceApi->all();
    $storyblokData = $storyblokResponse->data();
    expect($storyblokData->get("0.name"))
        ->toBe("Example Space")
        ->and($storyblokResponse->getResponseStatusCode())->toBe(200);
    foreach ($storyblokData as $spaceItem) {
        expect($spaceItem->name())
            ->toBeString();
    }

    expect($storyblokData->howManySpaces())->toBe(2);

});
