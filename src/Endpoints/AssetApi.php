<?php

declare(strict_types=1);

namespace Storyblok\Mapi\Endpoints;

use GuzzleHttp\Client;
use Storyblok\Mapi\Data\AssetData;
use Storyblok\Mapi\Data\AssetsData;
use Storyblok\Mapi\Data\SpaceData;
use Storyblok\Mapi\Data\SpacesData;
use Storyblok\Mapi\Data\StoryblokData;
use Storyblok\Mapi\Data\UserData;
use Storyblok\Mapi\StoryblokResponseInterface;
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
        $size = getimagesize($filename);
        $width = $size[0];
        $height = $size[1];
        $payload = [
            'filename' => $filename,
            'size' => $width . 'x' . $height,
            'validate_upload' => 1,
            'parent_id' => $parent_id,
        ];
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
        $postFields = $signedResponseData->get("fields")->toArray();
        $postFields['file'] = fopen($filename, 'r');
        $postUrl = $signedResponseData->get('post_url');

        $responseUpload = HttpClient::create()->request(
            "POST",
            $postUrl,
            [
                "body" => $postFields,
            ],
        );
        if ($responseUpload->getStatusCode() >= 200 && $responseUpload->getStatusCode() < 300) {
            echo "UPLOAD OK: " . $responseUpload->getStatusCode() . PHP_EOL;
        } else {
            throw new \Exception("Upload Asset, Upload call failed (Step 2) , " . $responseUpload->getStatusCode());
        }

        return $this->makeRequest(
            "GET",
            '/v1/spaces/' .
                $this->spaceId .
                '/assets/' .
                $signedResponseData->get('id') .
                '/finish_upload',
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
