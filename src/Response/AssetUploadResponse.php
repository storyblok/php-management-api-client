<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Response;

use Storyblok\ManagementApi\Data\Asset;
use Storyblok\ManagementApi\Data\Assets;
use Storyblok\ManagementApi\Data\Space;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class AssetUploadResponse extends StoryblokResponse implements StoryblokResponseInterface
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
    public function data(): Asset
    {
        //$key = "asset";
        $array = $this->toArray();
        //if (array_key_exists($key, $array)) {
        return Asset::make($array);
        //}

        //throw new StoryblokFormatException(sprintf("Expected '%s' in the response.", $key));
    }
}
