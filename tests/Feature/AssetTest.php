<?php

declare(strict_types=1);

use Storyblok\Mapi\MapiClient;

use Symfony\Component\HttpClient\MockHttpClient;


test('Testing One asset, AssetData', function (): void {
    $responses = [
        \mockResponse("one-asset", 200),
        \mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = MapiClient::initTest($client);
    $assetApi = $mapiClient->assetApi("222");

    $storyblokResponse = $assetApi->get("111");
    /** @var \Storyblok\Mapi\Data\AssetData $storyblokData */
    $storyblokData =  $storyblokResponse->data();
    expect($storyblokData->get("id"))
        ->toBe(111)
        ->and($storyblokData->filenameCDN())->toBe("https://a.storyblok.com/f/222/3799x6005/3af265ee08/mypic.jpg")
        ->and($storyblokResponse->getResponseStatusCode())->toBe(200);

    $storyblokResponse = $assetApi->get("111notexists");
    expect( $storyblokResponse->getResponseStatusCode())->toBe(404) ;
    expect( $storyblokResponse->asJson())->toBe('["This record could not be found"]');


});




