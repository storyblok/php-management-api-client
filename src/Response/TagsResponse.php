<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Response;

use Storyblok\ManagementApi\Data\Tags;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

class TagsResponse extends StoryblokResponse implements StoryblokResponseInterface
{
    #[\Override]
    public function data(): Tags
    {
        $key = "tags";
        $array = $this->toArray();
        if (array_key_exists($key, $array)) {
            return new Tags($array[$key]);
        }

        throw new StoryblokFormatException(sprintf("Expected '%s' in the response.", $key));
    }
}
