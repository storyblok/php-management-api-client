<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Response;

use Storyblok\ManagementApi\Data\AssetFolders;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

class AssetFoldersResponse extends StoryblokResponse implements StoryblokResponseInterface
{
    #[\Override]
    public function data(): AssetFolders
    {
        $key = "asset_folders";
        $array = $this->toArray();
        if (array_key_exists($key, $array)) {
            return new AssetFolders($array[$key]);
        }

        throw new StoryblokFormatException(sprintf("Expected '%s' in the response.", $key));
    }
}
