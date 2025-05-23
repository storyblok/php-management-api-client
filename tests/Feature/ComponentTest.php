<?php

declare(strict_types=1);

use Storyblok\ManagementApi\Data\Component;
use Storyblok\ManagementApi\Endpoints\ComponentApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Storyblok\ManagementApi\QueryParameters\ComponentsParams;
use Symfony\Component\HttpClient\MockHttpClient;

test('Testing list of components, Params', function (): void {
    $responses = [
        \mockResponse("list-components", 200, ),
        \mockResponse("list-components", 200, ),
        \mockResponse("list-components", 200, ),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $componentApi = new ComponentApi($mapiClient, "2222");

    $storyblokResponse = $componentApi->all();
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*\/v1\/spaces\/2222\/components$/');

    $storyblokResponse = $componentApi->all(new ComponentsParams(
        byIds: "1234567890",
    ));
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*\/v1\/spaces\/2222\/components\?by_ids\=1234567890$/');

    $storyblokResponse = $componentApi->all(new ComponentsParams(
        byIds: "1234567890",
        isRoot: true
    ));
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*\/v1\/spaces\/2222\/components\?is_root\=1\&by_ids\=1234567890$/');

});

test('Getting one component', function (): void {
    $responses = [
        \mockResponse("one-component", 200),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $componentApi = new ComponentApi($mapiClient, "2222");

    $componentResponse = $componentApi->get("7223149");
    $string = $componentResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*\/v1\/spaces\/2222\/components\/7223149$/');
    $component = $componentResponse->data();
    expect($component)->toBeInstanceOf(Component::class);

    expect($component->getInt("id"))->toBe(7223149);
    expect($component->id())->toBe("7223149");
    expect($component->name())->toBe("text-section");
    expect($component->createdAt())->toBe("2025-04-15T21:32:55.495Z");
    expect($component->updatedAt())->toBe("2025-04-15T21:32:55.495Z");
    expect($component->isRoot())->toBe(false);

});
