<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Storyblok\ManagementApi\Data\Asset;
use Storyblok\ManagementApi\Data\Assets;
use Storyblok\ManagementApi\Data\StoryblokData;
use Storyblok\ManagementApi\QueryParameters\AssetsParams;
use Storyblok\ManagementApi\QueryParameters\PaginationParams;
use Storyblok\ManagementApi\Response\AssetResponse;
use Storyblok\ManagementApi\Response\AssetsResponse;
use Storyblok\ManagementApi\Response\AssetUploadResponse;
use Storyblok\ManagementApi\Response\MessageResponse;
use Storyblok\ManagementApi\Response\StoryblokResponseInterface;
use Symfony\Component\HttpClient\HttpClient;

class AssetApi extends EndpointSpace
{
    /**
     * @link https://www.storyblok.com/docs/api/management/core-resources/assets/retrieve-multiple-assets
     * @param AssetsParams $params
     * @param PaginationParams $page
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function page(
        ?AssetsParams $params = null,
        ?PaginationParams $page = null,
    ): AssetsResponse {
        if (
            !$params instanceof
            \Storyblok\ManagementApi\QueryParameters\AssetsParams
        ) {
            $params = new AssetsParams();
        }

        if (
            !$page instanceof
            \Storyblok\ManagementApi\QueryParameters\PaginationParams
        ) {
            $page = new PaginationParams();
        }

        $options = [
            "query" => array_merge($params->toArray(), $page->toArray()),
        ];
        $httpResponse = $this->makeHttpRequest(
            "GET",
            "/v1/spaces/" . $this->spaceId . "/assets",
            options: $options,
        );

        return new AssetsResponse($httpResponse);
    }

    /**
     * Retrieving a single asset via the asset id.
     * @link https://www.storyblok.com/docs/api/management/core-resources/assets/retrieve-one-asset
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function get(string|int $assetId): AssetResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "GET",
            "/v1/spaces/" . $this->spaceId . "/assets/" . $assetId,
        );
        return new AssetResponse($httpResponse);
    }

    /**
     * @return array<mixed>
     */
    public function buildPayload(
        string $filename,
        string|int|null $parent_id = null,
    ): array {
        $payload = [
            "filename" => $filename,
            //'size' => $width . 'x' . $height,
            "validate_upload" => 1,
            "parent_id" => $parent_id,
        ];
        $size = getimagesize($filename);
        if ($size !== false) {
            $width = $size[0];
            $height = $size[1];
            $payload["size"] = $width . "x" . $height;
        }

        return $payload;
    }

    /**
     * Step 1: Create a signed request for uploading an asset.
     * @link https://www.storyblok.com/docs/api/management/core-resources/assets/create-an-asset
     * @param array<string, mixed> $payload The payload from buildPayload()
     * @return StoryblokResponseInterface The signed response containing post_url and fields
     * @throws \Exception If the signed request fails
     */
    public function createSignedRequest(array $payload): StoryblokResponseInterface
    {
        $signedResponse = $this->makeRequest(
            "POST",
            "/v1/spaces/" . $this->spaceId . "/assets/",
            ["body" => $payload],
        );

        if (!$signedResponse->isOk()) {
            throw new \Exception(
                "Upload Asset, Signed Request call failed (Step 1) , " .
                    $signedResponse->getResponseStatusCode() .
                    " - " .
                    $signedResponse->getErrorMessage(),
            );
        }

        return $signedResponse;
    }

    /**
     * Step 2: Upload the file to the signed URL (S3).
     * @param string $postUrl The signed URL to upload to
     * @param array<string, mixed> $postFields The fields from the signed response
     * @param string $filename The path to the file to upload
     * @return \Symfony\Contracts\HttpClient\ResponseInterface The upload response
     * @throws \Exception If the upload fails
     */
    public function uploadToSignedUrl(
        string $postUrl,
        array $postFields,
        string $filename,
    ): \Symfony\Contracts\HttpClient\ResponseInterface {
        $postFields["file"] = fopen($filename, "r");

        $responseUpload = $this->managementClient
            ->httpAssetClient()
            ->request("POST", $postUrl, [
                "body" => $postFields,
            ]);

        if (
            !(
                $responseUpload->getStatusCode() >= 200 &&
                $responseUpload->getStatusCode() < 300
            )
        ) {
            throw new \Exception(
                "Upload Asset, Upload call failed (Step 2) , " .
                    $responseUpload->getStatusCode(),
            );
        }

        return $responseUpload;
    }

    /**
     * Step 3: Finish the upload by notifying Storyblok.
     * @link https://www.storyblok.com/docs/api/management/core-resources/assets/finish-upload
     * @param string|int $assetId The asset ID from the signed response
     * @return AssetUploadResponse The final upload response
     */
    public function finishUpload(string|int $assetId): AssetUploadResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "GET",
            "/v1/spaces/" .
                $this->spaceId .
                "/assets/" .
                $assetId .
                "/finish_upload",
        );

        return new AssetUploadResponse($httpResponse);
    }

    /**
     * Upload a file to Storyblok assets.
     * This method orchestrates the 3-step upload process:
     * 1. createSignedRequest() - Get a signed URL for upload
     * 2. uploadToSignedUrl() - Upload the file to S3
     * 3. finishUpload() - Notify Storyblok that the upload is complete
     */
    public function upload(
        string $filename,
        string|int|null $parent_id = null,
    ): AssetUploadResponse {
        // Step 1: Create a signed request
        $payload = $this->buildPayload($filename, $parent_id);
        $signedResponse = $this->createSignedRequest($payload);
        $signedResponseData = $signedResponse->data();

        // Step 2: Upload file to the signed URL
        $fields = $signedResponseData->get("fields");
        $postFields = [];
        if ($fields instanceof StoryblokData) {
            $postFields = $fields->toArray();
        }

        $postUrl = $signedResponseData->getString("post_url");
        $this->uploadToSignedUrl($postUrl, $postFields, $filename);

        // Step 3: Finish the upload
        return $this->finishUpload($signedResponseData->getString("id"));
    }

    /**
     * @param $assetId
     */
    public function delete(string $assetId): AssetResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "DELETE",
            "/v1/spaces/" . $this->spaceId . "/assets/" . $assetId,
        );
        return new AssetResponse($httpResponse);
    }

    /**
     * Delete multiple assets via their IDs.
     * @link https://www.storyblok.com/docs/api/management/core-resources/assets/delete-multiple-assets
     * @param string[] $assetIds
     */
    public function deleteMultipleAssets(array $assetIds): MessageResponse
    {
        $payload = [
            "ids" => $assetIds,
        ];
        $httpResponse = $this->makeHttpRequest(
            "POST",
            "/v1/spaces/" . $this->spaceId . "/assets/bulk_destroy",
            [
                "body" => json_encode($payload),
            ],
        );
        return new MessageResponse($httpResponse);
    }
}
