<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Response;

use Storyblok\ManagementApi\Data\Assets;
use Storyblok\ManagementApi\Data\Space;
use Storyblok\ManagementApi\Data\Spaces;
use Storyblok\ManagementApi\Data\StoryblokData;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;
use Storyblok\ManagementApi\Response\StoryblokResponse;

class AssetsResponse extends StoryblokResponse implements StoryblokResponseInterface
{
    #[\Override]
    public function data(): Assets
    {
        $key = "assets";
        $array = $this->toArray();
        if (array_key_exists($key, $array)) {
            return new Assets($array[$key]);
        }

        throw new StoryblokFormatException(sprintf("Expected '%s' in the response.", $key));
    }
}
