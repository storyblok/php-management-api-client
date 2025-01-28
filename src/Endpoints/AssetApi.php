<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use GuzzleHttp\Client;
use Storyblok\ManagementApi\Data\AssetData;
use Storyblok\ManagementApi\Data\AssetsData;
use Storyblok\ManagementApi\Data\SpaceData;
use Storyblok\ManagementApi\Data\SpacesData;
use Storyblok\ManagementApi\Data\StoryblokData;
use Storyblok\ManagementApi\Data\UserData;
use Storyblok\ManagementApi\StoryblokResponseInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

/**
 *
 */
class AssetApi extends EndpointSpace
{
    public function page(int $page = 1, int $perPage = 25): StoryblokResponseInterface
    {
        $options = [
            'query' => [
                'page' => $page,
                'per_page' => $perPage,
            ],
        ];
        return $this->makeRequest(
            "GET",
            '/v1/spaces/' . $this->spaceId . '/assets',
            options: $options,
            dataClass: AssetsData::class,
        );
    }

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
