<?php

declare(strict_types=1);

use Storyblok\ManagementApi\Data\AssetsData;
use Storyblok\ManagementApi\Endpoints\AssetApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Storyblok\ManagementApi\QueryParameters\AssetsParams;
use Storyblok\ManagementApi\QueryParameters\PaginationParams;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;

test('Testing One asset, AssetData', function (): void {
    $responses = [
        \mockResponse("one-asset", 200),
        \mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $assetApi = new AssetApi($mapiClient, "222");

    $storyblokResponse = $assetApi->get("111");

    $storyblokData = $storyblokResponse->data();
    expect($storyblokData->get("id"))
        ->toBe(111)
        ->and($storyblokData->filenameCDN())->toBe("https://a.storyblok.com/f/222/3799x6005/3af265ee08/mypic.jpg")
        ->and($storyblokResponse->getResponseStatusCode())->toBe(200)
        ->and($storyblokData->contentType())->toBe("image/jpeg")
        ->and($storyblokData->contentLength())->toBe(3094788)
        ->and($storyblokData->createdAt())->toBe('2025-01-18')
        ->and($storyblokData->updatedAt())->toBe('2025-01-19');

    expect(
        function () use ($assetApi, $storyblokData): void {
            $storyblokResponse = $assetApi->get("111notexists");
        }
    )->toThrow(Exception::class, 'HTTP 404 returned for "https://example.com/v1/spaces/222/assets/111notexists');

    //$storyblokResponse = $assetApi->get("111notexists");
    //expect($storyblokResponse->getResponseStatusCode())->toBe(404) ;
    //expect($storyblokResponse->asJson())->toBe('["This record could not be found"]');

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

    /** @var AssetsData $storyblokData */
    $storyblokData = $storyblokResponse->data();
    foreach ($storyblokData as $asset) {
        expect($asset->id())->toBeGreaterThan(10);
    }

    expect($storyblokResponse->getResponseStatusCode())->toBe(200);
    expect($storyblokResponse->getErrorMessage())->toBe("No error detected, HTTP Status Code: 200") ;
    expect($storyblokResponse->total())->toBe(2);
    expect($storyblokResponse->perPage())->toBe(25);

    expect(function () use ($assetApi, $storyblokData): void {
        $storyblokResponse = $assetApi->page(page: new \Storyblok\ManagementApi\QueryParameters\PaginationParams(page: 100000));

    })->toThrow(
        \Symfony\Component\HttpClient\Exception\ClientException::class,
        'HTTP 404 returned for "https://example.com/v1/spaces/222/assets?page=100000&per_page=25'
    );

    //expect($storyblokResponse->getResponseStatusCode())->toBe(404) ;
    //expect($storyblokResponse->asJson())->toBe('["This record could not be found"]');
    //expect($storyblokResponse->isOk())->toBeFalse() ;
    //expect($storyblokResponse->getErrorMessage())->toStartWith("404 - Not Found.") ;

    $assetsData = AssetsData::make([]);
    expect($assetsData)->toBeInstanceOf(AssetsData::class);
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
        ),
        page: new PaginationParams(5, 30)
    );
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*in_folder=-1.*$/');
    expect($string)->toMatch('/.*page=5&per_page=30.*$/');

    $storyblokResponse = $assetApi->page(
        params: new AssetsParams(
            search: "something",
            withTags: "aaa"
        ),
        page: new PaginationParams(5, 30)
    );
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*search=something.*$/');
    expect($string)->toMatch('/.*with_tags=aaa.*$/');
    expect($string)->toMatch('/.*page=5&per_page=30.*$/');

});

test('testing asset payload', function (): void {
    $responses = [
        \mockResponse("list-assets", 200, ["total"=>2, "per-page" => 25 ]),
        \mockResponse("list-assets", 200, ["total"=>200, "per-page" => 25 ]),
        \mockResponse("list-assets", 200, ["total"=>200, "per-page" => 25 ]),
        \mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $assetApi = $mapiClient->assetApi("222");
    $filename = "./tests/Feature/Data/image-test.png";
    $parentId = "111";
    $payload = $assetApi->buildPayload($filename, $parentId);
    expect($payload)->toBeArray();
    expect($payload)->toHaveKey("filename");
});

test('delete one asset', function (): void {
    $responses = [
        \mockResponse("one-asset", 200, ["total"=>2, "per-page" => 25 ]),
        \mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $assetApi = new AssetApi($mapiClient, "222");
    $assetId = "12345";
    $response = $assetApi->delete($assetId);
    $data = $response->data();
    expect($data->id())->toBe("111");

});

test('upload one asset', function (): void {
    $responses = [
        \mockResponse('upload-asset-signed-response', 200),

        \mockResponse('one-asset', 200),
    ];
    $responsesAsset = [
        \mockResponse('one-asset', 200),
    ];

    $httpClient = new MockHttpClient($responses);
    $httpAssetClient = new MockHttpClient($responsesAsset);
    $mapiClient = ManagementApiClient::initTest($httpClient, $httpAssetClient);
    $assetApi = new AssetApi($mapiClient, "222");

    $response = $assetApi->upload("./tests/Feature/Data/image-test.png");
    $data = $response->data();
    expect($data->id())->toBe("111");

});

test('upload one asset - failing', function (): void {
    $responses = [
        \mockResponse('upload-asset-signed-response', 401),
    ];
    $responsesAsset = [
        \mockResponse('one-asset', 200),
    ];

    $httpClient = new MockHttpClient($responses);
    $httpAssetClient = new MockHttpClient($responsesAsset);
    $mapiClient = ManagementApiClient::initTest($httpClient, $httpAssetClient);
    $assetApi = new AssetApi($mapiClient, "222");

    $response = $assetApi->upload("./tests/Feature/Data/image-test.png");
    $data = $response->data();
    expect($data->id())->toBe("111");

})->throws(Exception::class);

test('upload one asset - failing on second step', function (): void {
    $responses = [
        \mockResponse('upload-asset-signed-response', 200),
    ];
    $responsesAsset = [
        \mockResponse('one-asset', 400),
    ];

    $httpClient = new MockHttpClient($responses);
    $httpAssetClient = new MockHttpClient($responsesAsset);
    $mapiClient = ManagementApiClient::initTest($httpClient, $httpAssetClient);
    $assetApi = new AssetApi($mapiClient, "222");

    $response = $assetApi->upload("./tests/Feature/Data/image-test.png");
    $data = $response->data();
    expect($data->id())->toBe("111");

})->throws(Exception::class);
