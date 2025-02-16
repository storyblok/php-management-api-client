<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Response;

use Storyblok\ManagementApi\Data\Space;
use Storyblok\ManagementApi\Data\Spaces;
use Storyblok\ManagementApi\Data\StoryblokData;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;
use Storyblok\ManagementApi\Response\StoryblokResponse;

class SpacesResponse extends StoryblokResponse implements StoryblokResponseInterface
{
    #[\Override]
    public function data(): Spaces
    {
        $key = "spaces";
        $array = $this->toArray();
        if (array_key_exists($key, $array)) {
            return new Spaces($array[$key]);
        }

        throw new StoryblokFormatException(sprintf("Expected '%s' in the response.", $key));
    }
}
