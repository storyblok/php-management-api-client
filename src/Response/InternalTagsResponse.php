<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Response;

use Storyblok\ManagementApi\Data\InternalTags;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

class InternalTagsResponse extends StoryblokResponse implements StoryblokResponseInterface
{
    #[\Override]
    public function data(): InternalTags
    {
        $key = "internal_tags";
        $array = $this->toArray();
        if (array_key_exists($key, $array)) {
            return new InternalTags($array[$key]);
        }

        throw new StoryblokFormatException(sprintf("Expected '%s' in the response.", $key));
    }
}
