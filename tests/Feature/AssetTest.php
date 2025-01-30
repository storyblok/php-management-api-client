<?php

declare(strict_types=1);

use Storyblok\ManagementApi\ManagementApiClient;

use Storyblok\ManagementApi\QueryParameters\AssetsParams;
use Storyblok\ManagementApi\QueryParameters\PaginationParams;
use Symfony\Component\HttpClient\MockHttpClient;


test('Testing One asset, AssetData', function (): void {
    $responses = [
        \mockResponse("one-asset", 200),
        \mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $assetApi = $mapiClient->assetApi("222");

    $storyblokResponse = $assetApi->get("111");
    /** @var \Storyblok\ManagementApi\Data\AssetData $storyblokData */
    $storyblokData =  $storyblokResponse->data();
    expect($storyblokData->get("id"))
        ->toBe(111)
        ->and($storyblokData->filenameCDN())->toBe("https://a.storyblok.com/f/222/3799x6005/3af265ee08/mypic.jpg")
        ->and($storyblokResponse->getResponseStatusCode())->toBe(200);

    $storyblokResponse = $assetApi->get("111notexists");
    expect( $storyblokResponse->getResponseStatusCode())->toBe(404) ;
    expect( $storyblokResponse->asJson())->toBe('["This record could not be found"]');


});

test('Testing list of assets, AssetsData', function (): void {
    $responses = [
        \mockResponse("list-assets", 200, ["total"=>2, "per-page" => 25 ]),
        \mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $assetApi = $mapiClient->assetApi("222");

    $storyblokResponse = $assetApi->page();

    /** @var \Storyblok\ManagementApi\Data\AssetsData $storyblokData */
    $storyblokData =  $storyblokResponse->data();
    foreach ($storyblokData as $asset) {
        expect($asset->id())->toBeGreaterThan(10);
    }

    expect($storyblokResponse->getResponseStatusCode())->toBe(200);
    expect( $storyblokResponse->getErrorMessage())->toBe("No error detected, HTTP Status Code: 200") ;
    expect($storyblokResponse->total())->toBe(2);
    expect($storyblokResponse->perPage())->toBe(25);

    $storyblokResponse = $assetApi->page(page: new \Storyblok\ManagementApi\QueryParameters\PaginationParams(page: 100000));
    expect( $storyblokResponse->getResponseStatusCode())->toBe(404) ;
    expect( $storyblokResponse->asJson())->toBe('["This record could not be found"]');
    expect( $storyblokResponse->isOk())->toBeFalse() ;
    expect( $storyblokResponse->getErrorMessage())->toStartWith("404 - Not Found.") ;
});


test('Testing list of assets, Params', function (): void {
    $responses = [
        \mockResponse("list-assets", 200, ["total"=>2, "per-page" => 25 ]),
        \mockResponse("list-assets", 200, ["total"=>200, "per-page" => 25 ]),
        \mockResponse("list-assets", 200, ["total"=>200, "per-page" => 25 ]),
        \mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $assetApi = $mapiClient->assetApi("222");

    $storyblokResponse = $assetApi->page(params: new AssetsParams(
        inFolder: -1
    ));
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*in_folder=-1.*$/');
    expect($string)->toMatch('/.*page=1&per_page=25.*$/');

    $storyblokResponse = $assetApi->page(
        params: new AssetsParams(
            inFolder: -1
        ),page: new PaginationParams(5, 30)
    );
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*in_folder=-1.*$/');
    expect($string)->toMatch('/.*page=5&per_page=30.*$/');

    $storyblokResponse = $assetApi->page(
        params: new AssetsParams(
            search: "something",
            withTags: "aaa"
        ),page: new PaginationParams(5, 30)
    );
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*search=something.*$/');
    expect($string)->toMatch('/.*with_tags=aaa.*$/');
    expect($string)->toMatch('/.*page=5&per_page=30.*$/');


});


