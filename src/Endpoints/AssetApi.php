<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Storyblok\ManagementApi\Data\AssetData;
use Storyblok\ManagementApi\Data\AssetsData;
use Storyblok\ManagementApi\Data\StoryblokData;
use Storyblok\ManagementApi\QueryParameters\AssetsParams;
use Storyblok\ManagementApi\QueryParameters\PaginationParams;
use Storyblok\ManagementApi\StoryblokResponseInterface;
use Symfony\Component\HttpClient\HttpClient;

/**
 *
 */
class AssetApi extends EndpointSpace
{
    /**
     * @link https://www.storyblok.com/docs/api/management/core-resources/assets/retrieve-multiple-assets
     * @param AssetsParams $params
     * @param PaginationParams $page
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function page(?AssetsParams $params = null, ?PaginationParams $page = null): StoryblokResponseInterface
    {
        if (!$params instanceof \Storyblok\ManagementApi\QueryParameters\AssetsParams) {
            $params = new AssetsParams();
        }

        if (!$page instanceof \Storyblok\ManagementApi\QueryParameters\PaginationParams) {
            $page = new PaginationParams();
        }

        $options = [
            'query' => array_merge($params->toArray(), $page->toArray()),
        ];
        return $this->makeRequest(
            "GET",
            '/v1/spaces/' . $this->spaceId . '/assets',
            options: $options,
            dataClass: AssetsData::class,
        );
    }

    /**
     * Retrieving a single asset via the asset id.
     * @link https://www.storyblok.com/docs/api/management/core-resources/assets/retrieve-one-asset
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function get(string|int $assetId): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "GET",
            '/v1/spaces/' . $this->spaceId . '/assets/' . $assetId,
            dataClass: AssetData::class,
        );
    }

    public function upload(string $filename, string|int|null $parent_id = null): StoryblokResponseInterface
    {
        // =========== CREATE A SIGNED REQUEST
        $payload = [
            'filename' => $filename,
            //'size' => $width . 'x' . $height,
            'validate_upload' => 1,
            'parent_id' => $parent_id,
        ];
        $size = getimagesize($filename);
        if ($size !== false) {
            $width = $size[0];
            $height = $size[1];
            $payload['size'] = $width . 'x' . $height;
        }

        $signedResponse = $this->makeRequest(
            "POST",
            '/v1/spaces/' . $this->spaceId . '/assets/',
            [ 'body' => $payload ],
        );
        if (! $signedResponse->isOk()) {
            throw new \Exception(
                "Upload Asset, Signed Request call failed (Step 1) , "
                . $signedResponse->getResponseStatusCode() . " - "
                . $signedResponse->getErrorMessage(),
            );
        }

        $signedResponseData = $signedResponse->data();

        // =========== UPLOAD FILE for the SIGNED REQUEST
        $fields = $signedResponseData->get("fields");
        $postFields = [];
        if ($fields instanceof StoryblokData) {
            $postFields = $fields->toArray();
        }

        $postFields['file'] = fopen($filename, 'r');
        $postUrl = $signedResponseData->getString('post_url');

        $responseUpload = HttpClient::create()->request(
            "POST",
            $postUrl,
            [
                "body" => $postFields,
            ],
        );
        if (!($responseUpload->getStatusCode() >= 200 && $responseUpload->getStatusCode() < 300)) {
            throw new \Exception("Upload Asset, Upload call failed (Step 2) , " . $responseUpload->getStatusCode());
        }

        return $this->makeRequest(
            "GET",
            '/v1/spaces/' .
                $this->spaceId .
                '/assets/' .
                $signedResponseData->getString('id') .
                '/finish_upload',
            dataClass: AssetData::class,
        );
    }

    /**
     * @param $assetId
     */
    public function delete(string $assetId): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "DELETE",
            '/v1/spaces/' . $this->spaceId . '/assets/' . $assetId,
        );
    }





}
