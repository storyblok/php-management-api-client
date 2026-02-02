<?php

declare(strict_types=1);

namespace Tests\Unit;

use Exception;
use Storyblok\ManagementApi\Endpoints\AssetApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Tests\TestCase;

final class AssetApiTest extends TestCase
{
    public function testCreateSignedRequestSuccess(): void
    {
        $responses = [
            $this->mockResponse("upload-asset-signed-response", 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetApi = new AssetApi($mapiClient, "222");

        $payload = [
            "filename" => "test-image.jpg",
            "validate_upload" => 1,
            "parent_id" => null,
        ];

        $response = $assetApi->createSignedRequest($payload);

        $this->assertTrue($response->isOk());
        $this->assertSame(200, $response->getResponseStatusCode());
        $this->assertSame(
            "https://example.com/v1/spaces/222/assets/",
            $response->getLastCalledUrl(),
        );

        $data = $response->data();
        $this->assertSame(140773191961982, $data->get("id"));
        $this->assertSame(
            "https://s3.amazonaws.com/a-example.storyblok.com",
            $data->getString("post_url"),
        );
        $this->assertNotNull($data->get("fields"));
    }

    public function testCreateSignedRequestWithParentId(): void
    {
        $responses = [
            $this->mockResponse("upload-asset-signed-response", 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetApi = new AssetApi($mapiClient, "222");

        $payload = [
            "filename" => "test-image.jpg",
            "validate_upload" => 1,
            "parent_id" => "12345",
        ];

        $response = $assetApi->createSignedRequest($payload);

        $this->assertTrue($response->isOk());
        $this->assertSame(200, $response->getResponseStatusCode());
        $this->assertSame(
            "https://example.com/v1/spaces/222/assets/",
            $response->getLastCalledUrl(),
        );
    }

    public function testCreateSignedRequestForDifferentSpace(): void
    {
        $responses = [
            $this->mockResponse("upload-asset-signed-response", 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetApi = new AssetApi($mapiClient, "999");

        $payload = [
            "filename" => "test-image.jpg",
            "validate_upload" => 1,
            "parent_id" => null,
        ];

        $response = $assetApi->createSignedRequest($payload);

        $this->assertSame(
            "https://example.com/v1/spaces/999/assets/",
            $response->getLastCalledUrl(),
        );
    }

    public function testCreateSignedRequestFailsWithUnauthorized(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Upload Asset, Signed Request call failed (Step 1)");

        $responses = [
            $this->mockResponse("upload-asset-signed-response", 401),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetApi = new AssetApi($mapiClient, "222");

        $payload = [
            "filename" => "test-image.jpg",
            "validate_upload" => 1,
            "parent_id" => null,
        ];

        $assetApi->createSignedRequest($payload);
    }

    public function testCreateSignedRequestFailsWithServerError(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Upload Asset, Signed Request call failed (Step 1)");

        $responses = [
            $this->mockResponse("upload-asset-signed-response", 500),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetApi = new AssetApi($mapiClient, "222");

        $payload = [
            "filename" => "test-image.jpg",
            "validate_upload" => 1,
            "parent_id" => null,
        ];

        $assetApi->createSignedRequest($payload);
    }

    public function testUploadToSignedUrlSuccess(): void
    {
        $responses = [];
        $responsesAsset = [
            $this->mockResponse("one-uploaded-asset", 200),
        ];

        $httpClient = new MockHttpClient($responses);
        $httpAssetClient = new MockHttpClient($responsesAsset);

        $mapiClient = ManagementApiClient::initTest($httpClient, $httpAssetClient);
        $assetApi = new AssetApi($mapiClient, "222");

        $postUrl = "https://s3.amazonaws.com/a-example.storyblok.com";
        $postFields = [
            "key" => "f/606/e5990a3595/your_file.jpg",
            "acl" => "public-read",
            "Content-Type" => "image/png",
            "policy" => "base64-encoded-policy",
            "x-amz-credential" => "AKIAIU627EN23A/20181110/s3/aws4_request",
            "x-amz-algorithm" => "AWS4-HMAC-SHA256",
            "x-amz-date" => "20181110T153300Z",
            "x-amz-signature" => "aaedd72b54636662b137b7648b54bdb47ee3b1dd28173313647930e625c8",
        ];
        $filename = "./tests/Feature/Data/image-test.png";

        $response = $assetApi->uploadToSignedUrl($postUrl, $postFields, $filename);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(
            "https://s3.amazonaws.com/a-example.storyblok.com",
            $response->getInfo("url"),
        );
    }

    public function testUploadToSignedUrlSuccessWithCreatedStatus(): void
    {
        $responses = [];
        $responsesAsset = [
            $this->mockResponse("one-uploaded-asset", 201),
        ];

        $httpClient = new MockHttpClient($responses);
        $httpAssetClient = new MockHttpClient($responsesAsset);

        $mapiClient = ManagementApiClient::initTest($httpClient, $httpAssetClient);
        $assetApi = new AssetApi($mapiClient, "222");

        $postUrl = "https://s3.amazonaws.com/a-example.storyblok.com";
        $postFields = [
            "key" => "f/606/e5990a3595/your_file.jpg",
        ];
        $filename = "./tests/Feature/Data/image-test.png";

        $response = $assetApi->uploadToSignedUrl($postUrl, $postFields, $filename);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame(
            "https://s3.amazonaws.com/a-example.storyblok.com",
            $response->getInfo("url"),
        );
    }

    public function testUploadToSignedUrlFailsWithBadRequest(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Upload Asset, Upload call failed (Step 2) , 400");

        $responses = [];
        $responsesAsset = [
            $this->mockResponse("one-uploaded-asset", 400),
        ];

        $httpClient = new MockHttpClient($responses);
        $httpAssetClient = new MockHttpClient($responsesAsset);

        $mapiClient = ManagementApiClient::initTest($httpClient, $httpAssetClient);
        $assetApi = new AssetApi($mapiClient, "222");

        $postUrl = "https://s3.amazonaws.com/a-example.storyblok.com";
        $postFields = [
            "key" => "f/606/e5990a3595/your_file.jpg",
        ];
        $filename = "./tests/Feature/Data/image-test.png";

        $assetApi->uploadToSignedUrl($postUrl, $postFields, $filename);
    }

    public function testUploadToSignedUrlFailsWithForbidden(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Upload Asset, Upload call failed (Step 2) , 403");

        $responses = [];
        $responsesAsset = [
            $this->mockResponse("one-uploaded-asset", 403),
        ];

        $httpClient = new MockHttpClient($responses);
        $httpAssetClient = new MockHttpClient($responsesAsset);

        $mapiClient = ManagementApiClient::initTest($httpClient, $httpAssetClient);
        $assetApi = new AssetApi($mapiClient, "222");

        $postUrl = "https://s3.amazonaws.com/a-example.storyblok.com";
        $postFields = [];
        $filename = "./tests/Feature/Data/image-test.png";

        $assetApi->uploadToSignedUrl($postUrl, $postFields, $filename);
    }

    public function testFinishUploadSuccess(): void
    {
        $responses = [
            $this->mockResponse("one-uploaded-asset", 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetApi = new AssetApi($mapiClient, "222");

        $response = $assetApi->finishUpload("140773191961982");

        $this->assertSame(200, $response->getResponseStatusCode());
        $this->assertSame(
            "https://example.com/v1/spaces/222/assets/140773191961982/finish_upload",
            $response->getLastCalledUrl(),
        );

        $data = $response->data();
        $this->assertSame("111", $data->id());
    }

    public function testFinishUploadWithIntegerId(): void
    {
        $responses = [
            $this->mockResponse("one-uploaded-asset", 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetApi = new AssetApi($mapiClient, "222");

        $response = $assetApi->finishUpload(140773191961982);

        $this->assertSame(200, $response->getResponseStatusCode());
        $this->assertSame(
            "https://example.com/v1/spaces/222/assets/140773191961982/finish_upload",
            $response->getLastCalledUrl(),
        );
    }

    public function testFinishUploadForDifferentSpace(): void
    {
        $responses = [
            $this->mockResponse("one-uploaded-asset", 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetApi = new AssetApi($mapiClient, "999");

        $response = $assetApi->finishUpload("12345");

        $this->assertSame(
            "https://example.com/v1/spaces/999/assets/12345/finish_upload",
            $response->getLastCalledUrl(),
        );
    }

    public function testFinishUploadReturnsAssetData(): void
    {
        $responses = [
            $this->mockResponse("one-uploaded-asset", 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $assetApi = new AssetApi($mapiClient, "222");

        $response = $assetApi->finishUpload("12345");
        $data = $response->data();

        $this->assertSame("111", $data->id());
        $this->assertSame(222, $data->get("space_id"));
        $this->assertSame("mypic.jpg", $data->get("short_filename"));
        $this->assertSame("image/jpeg", $data->get("content_type"));
    }

    public function testFullUploadFlowWithSeparateMethods(): void
    {
        $responses = [
            $this->mockResponse("upload-asset-signed-response", 200),
            $this->mockResponse("one-uploaded-asset", 200),
        ];

        $responsesAsset = [
            $this->mockResponse("one-uploaded-asset", 200),
        ];

        $httpClient = new MockHttpClient($responses);
        $httpAssetClient = new MockHttpClient($responsesAsset);

        $mapiClient = ManagementApiClient::initTest($httpClient, $httpAssetClient);
        $assetApi = new AssetApi($mapiClient, "222");

        // Step 1: Create signed request
        $payload = [
            "filename" => "./tests/Feature/Data/image-test.png",
            "validate_upload" => 1,
            "parent_id" => null,
        ];
        $signedResponse = $assetApi->createSignedRequest($payload);
        $signedData = $signedResponse->data();

        $this->assertSame(
            "https://example.com/v1/spaces/222/assets/",
            $signedResponse->getLastCalledUrl(),
        );
        $this->assertSame(140773191961982, $signedData->get("id"));
        $this->assertSame(
            "https://s3.amazonaws.com/a-example.storyblok.com",
            $signedData->getString("post_url"),
        );

        // Step 2: Upload to signed URL
        $fields = $signedData->get("fields");
        $postFields = $fields instanceof \Storyblok\ManagementApi\Data\StoryblokData
            ? $fields->toArray()
            : [];
        $postUrl = $signedData->getString("post_url");

        $uploadResponse = $assetApi->uploadToSignedUrl(
            $postUrl,
            $postFields,
            "./tests/Feature/Data/image-test.png",
        );
        $this->assertSame(200, $uploadResponse->getStatusCode());
        $this->assertSame(
            "https://s3.amazonaws.com/a-example.storyblok.com",
            $uploadResponse->getInfo("url"),
        );

        // Step 3: Finish upload
        $assetId = $signedData->getString("id");
        $finishResponse = $assetApi->finishUpload($assetId);

        $this->assertSame(200, $finishResponse->getResponseStatusCode());
        $this->assertSame(
            "https://example.com/v1/spaces/222/assets/140773191961982/finish_upload",
            $finishResponse->getLastCalledUrl(),
        );
        $this->assertSame("111", $finishResponse->data()->id());
    }
}
