<?php

declare(strict_types=1);

use Storyblok\Mapi\MapiClient;

use Symfony\Component\HttpClient\MockHttpClient;


test('Testing multiple resources, StoryblokData', function (): void {
    $responses = [
        \mockResponse("list-tags", 200,
            ["total"=>8, "per-page" => 25 ]),
        \mockResponse("empty-tags", 404),
    ];

    $client = new MockHttpClient($responses, baseUri: 'https://mapi.storyblok.com');
    $mapiClient = MapiClient::initTest($client);
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
            "search" => "somethingnotexists"
        ]
    );
    expect( $response->getResponseStatusCode())->toBe(404) ;
    expect( $response->asJson())->toBe('["This record could not be found"]');


});




