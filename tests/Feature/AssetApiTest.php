<?php

declare(strict_types=1);

namespace Tests\Feature;

use Exception;
use Tests\TestCase;
use Storyblok\ManagementApi\Data\Asset;
use Storyblok\ManagementApi\Data\Assets;
use Storyblok\ManagementApi\Data\Fields\AssetField;
use Storyblok\ManagementApi\Data\StoryComponent;
use Storyblok\ManagementApi\Endpoints\AssetApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Storyblok\ManagementApi\QueryParameters\AssetsParams;
use Storyblok\ManagementApi\QueryParameters\PaginationParams;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\MockHttpClient;

final class AssetApiTest extends TestCase
{
    public function testOneAssetAssetData(): void
    {
        $responses = [
            $this->mockResponse("one-asset", 200),
            $this->mockResponse("empty-asset", 404),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetApi = new AssetApi($mapiClient, "222");

        $storyblokResponse = $assetApi->get("111");
        $storyblokData = $storyblokResponse->data();

        $this->assertSame(111, $storyblokData->get("id"));
        $this->assertSame(
            "https://a.storyblok.com/f/222/3799x6005/3af265ee08/mypic.jpg",
            $storyblokData->filenameCDN(),
        );
        $this->assertSame(200, $storyblokResponse->getResponseStatusCode());
        $this->assertSame("image/jpeg", $storyblokData->contentType());
        $this->assertSame(3_094_788, $storyblokData->contentLength());
        $this->assertSame("2025-01-18", $storyblokData->createdAt());
        $this->assertSame("2025-01-19", $storyblokData->updatedAt());

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            'HTTP 404 returned for "https://example.com/v1/spaces/222/assets/111notexists',
        );

        $assetApi->get("111notexists");
    }

    public function testListAssetsAssetsData(): void
    {
        $responses = [
            $this->mockResponse("list-assets", 200, [
                "total" => 2,
                "per-page" => 25,
            ]),
            $this->mockResponse("empty-asset", 404),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetApi = new AssetApi($mapiClient, "222");

        $storyblokResponse = $assetApi->page();
        $storyblokData = $storyblokResponse->data();
        $this->assertCount(2, $storyblokData);
        //$this->assertInstanceOf(Assets::class, $storyblokData);

        foreach ($storyblokData as $asset) {
            $this->assertInstanceOf(Asset::class, $asset);
            $this->assertGreaterThan(10, $asset->id());
        }

        $this->assertSame(200, $storyblokResponse->getResponseStatusCode());
        $this->assertSame(
            "No error detected, HTTP Status Code: 200",
            $storyblokResponse->getErrorMessage(),
        );
        $this->assertSame(2, $storyblokResponse->total());
        $this->assertSame(25, $storyblokResponse->perPage());

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage(
            'HTTP 404 returned for "https://example.com/v1/spaces/222/assets?page=100000&per_page=25',
        );

        $assetApi->page(page: new PaginationParams(page: 100000));
    }

    public function testListAssetsParams(): void
    {
        $responses = [
            $this->mockResponse("list-assets", 200, [
                "total" => 2,
                "per-page" => 25,
            ]),
            $this->mockResponse("list-assets", 200, [
                "total" => 200,
                "per-page" => 25,
            ]),
            $this->mockResponse("list-assets", 200, [
                "total" => 200,
                "per-page" => 25,
            ]),
            $this->mockResponse("empty-asset", 404),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetApi = new AssetApi($mapiClient, "222");

        $response = $assetApi->page(params: new AssetsParams(inFolder: -1));

        $url = $response->getLastCalledUrl();
        $this->assertMatchesRegularExpression('/.*in_folder=-1.*$/', $url);
        $this->assertMatchesRegularExpression(
            '/.*page=1&per_page=25.*$/',
            $url,
        );

        $response = $assetApi->page(
            params: new AssetsParams(inFolder: -1),
            page: new PaginationParams(5, 30),
        );

        $url = $response->getLastCalledUrl();
        $this->assertMatchesRegularExpression(
            '/.*page=5&per_page=30.*$/',
            $url,
        );

        $response = $assetApi->page(
            params: new AssetsParams(search: "something", withTags: "aaa"),
            page: new PaginationParams(5, 30),
        );

        $url = $response->getLastCalledUrl();
        $this->assertMatchesRegularExpression('/.*search=something.*$/', $url);
        $this->assertMatchesRegularExpression('/.*with_tags=aaa.*$/', $url);
    }

    public function testAssetPayload(): void
    {
        $responses = [
            $this->mockResponse("list-assets", 200),
            $this->mockResponse("list-assets", 200),
            $this->mockResponse("list-assets", 200),
            $this->mockResponse("empty-asset", 404),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetApi = new AssetApi($mapiClient, "222");

        $payload = $assetApi->buildPayload(
            "./tests/Feature/Data/image-test.png",
            "111",
        );

        $this->assertIsArray($payload);
        $this->assertArrayHasKey("filename", $payload);
    }

    public function testDeleteOneAsset(): void
    {
        $responses = [
            $this->mockResponse("one-asset", 200),
            $this->mockResponse("empty-asset", 404),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetApi = new AssetApi($mapiClient, "222");

        $response = $assetApi->delete("12345");
        $data = $response->data();

        $this->assertSame("111", $data->id());
    }

    public function testDeleteMultipleAsset(): void
    {
        $responses = [
            $this->mockResponse("delete-multiple-assets", 200),
            $this->mockResponse("empty-asset", 404),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetApi = new AssetApi($mapiClient, "222");

        $response = $assetApi->deleteMultipleAssets(["123456789", "987654321"]);
        $data = $response->data();

        $this->assertSame(
            "Asset(s) 123456789, 987654321 deleted successfully",
            $data->message(),
        );
    }

    public function testUploadOneAsset(): void
    {
        $responses = [
            $this->mockResponse("upload-asset-signed-response", 200),
            $this->mockResponse("one-uploaded-asset", 200),
        ];

        $responsesAsset = [$this->mockResponse("one-uploaded-asset", 200)];

        $httpClient = new MockHttpClient($responses);
        $httpAssetClient = new MockHttpClient($responsesAsset);

        $mapiClient = ManagementApiClient::initTest(
            $httpClient,
            $httpAssetClient,
        );
        $assetApi = new AssetApi($mapiClient, "222");

        $response = $assetApi->upload("./tests/Feature/Data/image-test.png");
        $this->assertSame(
            "https://example.com/v1/spaces/222/assets/140773191961982/finish_upload",
            $response->getLastCalledUrl(),
        );
        $data = $response->data();

        $this->assertSame("111", $data->id());
    }

    public function testUploadOneAssetFailing(): void
    {
        $this->expectException(Exception::class);

        $responses = [$this->mockResponse("upload-asset-signed-response", 401)];

        $responsesAsset = [$this->mockResponse("one-uploaded-asset", 200)];

        $httpClient = new MockHttpClient($responses);
        $httpAssetClient = new MockHttpClient($responsesAsset);

        $mapiClient = ManagementApiClient::initTest(
            $httpClient,
            $httpAssetClient,
        );
        $assetApi = new AssetApi($mapiClient, "222");

        $assetApi->upload("./tests/Feature/Data/image-test.png");
    }

    public function testUploadOneAssetFailingSecondStep(): void
    {
        $this->expectException(Exception::class);

        $responses = [$this->mockResponse("upload-asset-signed-response", 200)];

        $responsesAsset = [$this->mockResponse("one-uploaded-asset", 400)];

        $httpClient = new MockHttpClient($responses);
        $httpAssetClient = new MockHttpClient($responsesAsset);

        $mapiClient = ManagementApiClient::initTest(
            $httpClient,
            $httpAssetClient,
        );
        $assetApi = new AssetApi($mapiClient, "222");

        $assetApi->upload("./tests/Feature/Data/image-test.png");
    }

    public function testAssetFieldHandling(): void
    {
        $responses = [
            $this->mockResponse("one-asset", 200),
            $this->mockResponse("empty-asset", 404),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetApi = new AssetApi($mapiClient, "222");

        $asset = $assetApi->get("111")->data();
        $assetField = AssetField::makeFromAsset($asset);

        $this->assertSame(111, $assetField->get("id"));
        $this->assertSame(
            "https://a.storyblok.com/f/222/3799x6005/3af265ee08/mypic.jpg",
            $assetField->get("filename"),
        );
        $this->assertSame("", $assetField->getString("title"));
    }

    public function testSetAssetInStoryComponent(): void
    {
        $responses = [
            $this->mockResponse("one-asset", 200),
            $this->mockResponse("empty-asset", 404),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetApi = new AssetApi($mapiClient, "222");

        $asset = $assetApi->get("111")->data();

        $content = new StoryComponent("article-page");
        $content->set("title", "My New Article");
        $content->setAsset("image", $asset);

        $this->assertNull($content->get("id"));
        $this->assertSame(
            "https://a.storyblok.com/f/222/3799x6005/3af265ee08/mypic.jpg",
            $content->get("image.filename"),
        );
        $this->assertSame("asset", $content->get("image.fieldtype"));
        $this->assertSame("My New Article", $content->get("title"));
    }
}
