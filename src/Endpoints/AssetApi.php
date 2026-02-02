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
use Storyblok\ManagementApi\Response\StoryblokResponse;
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

    public function upload(
        string $filename,
        string|int|null $parent_id = null,
    ): AssetUploadResponse {
        // =========== CREATE A SIGNED REQUEST
        $payload = $this->buildPayload($filename, $parent_id);

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

        $signedResponseData = $signedResponse->data();

        // =========== UPLOAD FILE for the SIGNED REQUEST
        $fields = $signedResponseData->get("fields");
        $postFields = [];
        if ($fields instanceof StoryblokData) {
            $postFields = $fields->toArray();
        }

        $postFields["file"] = fopen($filename, "r");
        $postUrl = $signedResponseData->getString("post_url");

        /*
        $responseUpload = $this->makeHttpRequest(
            "POST",
            $postUrl,
            [
                "body" => $postFields,
            ],

        );
        */

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
            //var_dump($responseUpload->getInfo());
            throw new \Exception(
                "Upload Asset, Upload call failed (Step 2) , " .
                    $responseUpload->getStatusCode(),
            );
        }

        $httpResponse = $this->makeHttpRequest(
            "GET",
            "/v1/spaces/" .
                $this->spaceId .
                "/assets/" .
                $signedResponseData->getString("id") .
                "/finish_upload",
        );
        return new AssetUploadResponse($httpResponse);
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
