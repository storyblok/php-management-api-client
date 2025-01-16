<?php

declare(strict_types=1);

use Storyblok\Mapi\MapiClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\MockHttpClient;


test('Testing One Story, StoryData', function (): void {
    $responses = [
        \mockResponse("one-story", 200),
        \mockResponse("empty-story", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = MapiClient::initTest($client);
    $storyApi = $mapiClient->storyApi("222");

    $storyblokResponse = $storyApi->get("111");
    /** @var \Storyblok\Mapi\Data\StoryData $storyblokData */
    $storyblokData =  $storyblokResponse->data();
    expect($storyblokData->get("name"))
        ->toBe("My third post")
        ->and($storyblokData->name())->toBe("My third post")
        ->and($storyblokData->createdAt())->toBe("2024-02-08")
        ->and($storyblokResponse->getResponseStatusCode())->toBe(200);

    $storyblokResponse = $storyApi->get("111notexists");
    expect( $storyblokResponse->getResponseStatusCode())->toBe(404) ;
    expect( $storyblokResponse->asJson())->toBe('["This record could not be found"]');


});




