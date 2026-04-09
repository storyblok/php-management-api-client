<?php

declare(strict_types=1);

namespace Tests\Feature;

use Exception;
use Storyblok\ManagementApi\Data\AssetFolder;
use Storyblok\ManagementApi\Endpoints\AssetFolderApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\MockHttpClient;
use Tests\TestCase;

final class AssetFolderApiTest extends TestCase
{
    public function testListAssetFolders(): void
    {
        $responses = [
            $this->mockResponse("list-asset-folders", 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetFolderApi = new AssetFolderApi($mapiClient, "222");

        $response = $assetFolderApi->page();
        $data = $response->data();

        $this->assertSame(200, $response->getResponseStatusCode());
        $this->assertCount(3, $data);

        $this->assertContainsOnlyInstancesOf(AssetFolder::class, $data);
    }

    public function testListAssetFoldersUrl(): void
    {
        $responses = [
            $this->mockResponse("list-asset-folders", 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetFolderApi = new AssetFolderApi($mapiClient, "222");

        $response = $assetFolderApi->page();

        $this->assertSame(
            "https://example.com/v1/spaces/222/asset_folders",
            $response->getLastCalledUrl(),
        );
    }

    public function testGetOneAssetFolder(): void
    {
        $responses = [
            $this->mockResponse("one-asset-folder", 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetFolderApi = new AssetFolderApi($mapiClient, "222");

        $response = $assetFolderApi->get("100");
        $data = $response->data();

        $this->assertSame(200, $response->getResponseStatusCode());
        $this->assertSame("100", $data->id());
        $this->assertSame("Images", $data->name());
        $this->assertNull($data->parentId());
        $this->assertSame(
            "https://example.com/v1/spaces/222/asset_folders/100",
            $response->getLastCalledUrl(),
        );
    }

    public function testGetOneAssetFolderNotFound(): void
    {
        $responses = [
            $this->mockResponse("one-asset-folder", 404),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetFolderApi = new AssetFolderApi($mapiClient, "222");

        $this->expectException(ClientException::class);
        $assetFolderApi->get("999");
    }

    public function testCreateAssetFolder(): void
    {
        $responses = [
            $this->mockResponse("one-asset-folder", 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetFolderApi = new AssetFolderApi($mapiClient, "222");

        $folder = new AssetFolder("Images");
        $response = $assetFolderApi->create($folder);
        $data = $response->data();

        $this->assertSame(200, $response->getResponseStatusCode());
        $this->assertSame("Images", $data->name());
        $this->assertSame(
            "https://example.com/v1/spaces/222/asset_folders",
            $response->getLastCalledUrl(),
        );
    }

    public function testUpdateAssetFolder(): void
    {
        $responses = [
            $this->mockResponse("one-asset-folder", 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetFolderApi = new AssetFolderApi($mapiClient, "222");

        $folder = new AssetFolder("Images Renamed");
        $response = $assetFolderApi->update("100", $folder);

        $this->assertSame(200, $response->getResponseStatusCode());
        $this->assertSame(
            "https://example.com/v1/spaces/222/asset_folders/100",
            $response->getLastCalledUrl(),
        );
    }

    public function testDeleteAssetFolder(): void
    {
        $responses = [
            $this->mockResponse("one-asset-folder", 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetFolderApi = new AssetFolderApi($mapiClient, "222");

        $response = $assetFolderApi->delete("100");

        $this->assertSame(200, $response->getResponseStatusCode());
        $this->assertSame(
            "https://example.com/v1/spaces/222/asset_folders/100",
            $response->getLastCalledUrl(),
        );
    }

    public function testAssetFolderForDifferentSpace(): void
    {
        $responses = [
            $this->mockResponse("list-asset-folders", 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetFolderApi = new AssetFolderApi($mapiClient, "999");

        $response = $assetFolderApi->page();

        $this->assertSame(
            "https://example.com/v1/spaces/999/asset_folders",
            $response->getLastCalledUrl(),
        );
    }

    public function testAssetFolderWithParentId(): void
    {
        $responses = [
            $this->mockResponse("list-asset-folders", 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetFolderApi = new AssetFolderApi($mapiClient, "222");

        $data = $assetFolderApi->page()->data();
        $folders = [];
        foreach ($data as $folder) {
            $folders[] = $folder;
        }

        // Third folder "Thumbnails" has parent_id = 100
        $this->assertInstanceOf(AssetFolder::class, $folders[2]);
        $this->assertSame("300", $folders[2]->id());
        $this->assertSame("Thumbnails", $folders[2]->name());
        $this->assertSame(100, $folders[2]->parentId());
    }
}
