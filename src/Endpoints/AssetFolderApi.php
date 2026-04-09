<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Storyblok\ManagementApi\Data\AssetFolder;
use Storyblok\ManagementApi\Response\AssetFolderResponse;
use Storyblok\ManagementApi\Response\AssetFoldersResponse;

class AssetFolderApi extends EndpointSpace
{
    /**
     * Retrieve all asset folders.
     * @link https://www.storyblok.com/docs/api/management/core-resources/asset-folders/retrieve-multiple-asset-folders
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function page(): AssetFoldersResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "GET",
            "/v1/spaces/" . $this->spaceId . "/asset_folders",
        );
        return new AssetFoldersResponse($httpResponse);
    }

    /**
     * Retrieve a single asset folder.
     * @link https://www.storyblok.com/docs/api/management/core-resources/asset-folders/retrieve-one-asset-folder
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function get(string|int $assetFolderId): AssetFolderResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "GET",
            "/v1/spaces/" . $this->spaceId . "/asset_folders/" . $assetFolderId,
        );
        return new AssetFolderResponse($httpResponse);
    }

    /**
     * Create a new asset folder.
     * @link https://www.storyblok.com/docs/api/management/core-resources/asset-folders/create-an-asset-folder
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function create(AssetFolder $assetFolderData): AssetFolderResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "POST",
            "/v1/spaces/" . $this->spaceId . "/asset_folders",
            [
                "body" => json_encode(["asset_folder" => $assetFolderData->toArray()]),
            ],
        );
        return new AssetFolderResponse($httpResponse);
    }

    /**
     * Update an existing asset folder.
     * @link https://www.storyblok.com/docs/api/management/core-resources/asset-folders/update-an-asset-folder
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function update(string|int $assetFolderId, AssetFolder $assetFolderData): AssetFolderResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "PUT",
            "/v1/spaces/" . $this->spaceId . "/asset_folders/" . $assetFolderId,
            [
                "body" => json_encode(["asset_folder" => $assetFolderData->toArray()]),
            ],
        );
        return new AssetFolderResponse($httpResponse);
    }

    /**
     * Delete an asset folder.
     * @link https://www.storyblok.com/docs/api/management/core-resources/asset-folders/delete-an-asset-folder
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function delete(string|int $assetFolderId): AssetFolderResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "DELETE",
            "/v1/spaces/" . $this->spaceId . "/asset_folders/" . $assetFolderId,
        );
        return new AssetFolderResponse($httpResponse);
    }
}
