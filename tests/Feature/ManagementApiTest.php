<?php

declare(strict_types=1);

use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Component\HttpClient\MockHttpClient;

test('Testing multiple resources, StoryblokData', function (): void {
    $responses = [
        \mockResponse(
            "list-tags",
            200,
            ["total"=>8, "per-page" => 25 ]
        ),
        \mockResponse("empty-tags", 404),
    ];

    $client = new MockHttpClient($responses, baseUri: 'https://mapi.storyblok.com');
    $mapiClient = ManagementApiClient::initTest($client);
    $managementApi = $mapiClient->managementApi();

    $spaceId = "321388";
    $response = $managementApi->get(
        sprintf('spaces/%s/internal_tags', $spaceId),
        [
            "by_object_type" => "asset",
            //"search" => "so"
        ]
    );
    expect($response->getLastCalledUrl())->toBe(
        "https://mapi.storyblok.com/v1/spaces/321388/internal_tags?by_object_type=asset"
    );
    expect($response->total())->toBe(8);
    expect($response->isOk())->toBe(true);

    $tags = $response->data()->get("internal_tags");
    expect($tags->count())->toBe(8);
    foreach ($tags as $tag) {
        expect($tag->get("object_type"))->toBe("asset");
        expect($tag->get("name"))->toBeString();
        expect($tag->get("id"))->toBeNumeric()->toBeGreaterThan(1000);
    }

    $response = $managementApi->get(
        sprintf('spaces/%s/internal_tags', $spaceId),
        [
            "by_object_type" => "asset",
            "search" => "somethingnotexists",
        ]
    );
    expect($response->getResponseStatusCode())->toBe(404) ;
    expect($response->asJson())->toBe('["This record could not be found"]');

});

test('Testing create resource, StoryblokData', function (): void {
    $responses = [
        \mockResponse("one-tag", 200),
        \mockResponse("empty-tags", 404),
    ];

    $client = new MockHttpClient($responses, baseUri: 'https://mapi.storyblok.com');
    $mapiClient = ManagementApiClient::initTest($client);
    $managementApi = $mapiClient->managementApi();

    // CREATE A TAG
    $tag = [
        "name" => "new tag",
        "object_type" => "asset",
    ];
    $spaceId = "321388";
    $response = $managementApi->post(
        sprintf('spaces/%s/internal_tags', $spaceId),
        ["internal_tag" => $tag ]
    );
    expect($response->isOk())->toBe(true);
    $tag = $response->data()->get("internal_tag");
    expect($tag->get("name"))->toBeString();
    expect($tag->getString("name"))->toBeString();
    expect($tag->getString("name"))->toBe("some");

});

test('Testing delete resource, StoryblokData', function (): void {
    $responses = [
        \mockResponse("one-tag", 200),
        \mockResponse("empty-tags", 404),
    ];

    $client = new MockHttpClient($responses, baseUri: 'https://mapi.storyblok.com');
    $mapiClient = ManagementApiClient::initTest($client);
    $managementApi = $mapiClient->managementApi();

    // CREATE A TAG
    $tag = [
        "name" => "new tag",
        "object_type" => "asset",
    ];
    $spaceId = "321388";
    $tagId = "56980";
    $response = $managementApi->delete(
        sprintf('spaces/%s/internal_tags/%s', $spaceId, $tagId)
    );
    expect($response->isOk())->toBe(true);
    $tag = $response->data()->get("internal_tag");
    expect($tag->get("name"))->toBeString();
    expect($tag->getString("name"))->toBeString();
    expect($tag->getString("name"))->toBe("some");

});

test('Testing edit resource, StoryblokData', function (): void {
    $responses = [
        \mockResponse("one-tag", 200),
        \mockResponse("empty-tags", 404),
    ];

    $client = new MockHttpClient($responses, baseUri: 'https://mapi.storyblok.com');
    $mapiClient = ManagementApiClient::initTest($client);
    $managementApi = $mapiClient->managementApi();

    // CREATE A TAG
    $tag = [
        "name" => "some",
        "object_type" => "asset",
    ];
    $spaceId = "321388";
    $tagId = "56980";
    $response = $managementApi->put(
        sprintf('spaces/%s/internal_tags/%s', $spaceId, $tagId)
    );
    expect($response->isOk())->toBe(true);

    $tag = $response->data()->get("internal_tag");
    expect($tag->get("name"))->toBeString();
    expect($tag->getString("name"))->toBeString();
    expect($tag->getString("name"))->toBe("some");

});

test('Testing StoryblokData', function (): void {
    $responses = [
        \mockResponse(
            "list-tags",
            200,
            ["total" => 8, "per-page" => 25]
        ),
        \mockResponse("empty-tags", 404),
    ];

    $client = new MockHttpClient($responses, baseUri: 'https://mapi.storyblok.com');
    $mapiClient = ManagementApiClient::initTest($client);
    $managementApi = $mapiClient->managementApi();

    $spaceId = "321388";
    $response = $managementApi->get(
        sprintf('spaces/%s/internal_tags', $spaceId),
        [
            "by_object_type" => "asset",
            //"search" => "so"
        ]
    );
    $array =
    expect($response->toArray())->toBeArray();

});
