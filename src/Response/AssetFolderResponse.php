<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Response;

use Storyblok\ManagementApi\Data\AssetFolder;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class AssetFolderResponse extends StoryblokResponse implements StoryblokResponseInterface
{
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws StoryblokFormatException
     * @throws ClientExceptionInterface
     */
    #[\Override]
    public function data(): AssetFolder
    {
        $key = "asset_folder";
        $array = $this->toArray();
        if (array_key_exists($key, $array)) {
            return AssetFolder::make($array[$key]);
        }

        throw new StoryblokFormatException(sprintf("Expected '%s' in the response.", $key));
    }
}
