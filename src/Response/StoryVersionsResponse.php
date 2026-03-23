<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Response;

use Storyblok\ManagementApi\Data\StoryVersions;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

class StoryVersionsResponse extends StoryblokResponse implements StoryblokResponseInterface
{
    #[\Override]
    public function data(): StoryVersions
    {
        $key = "story_versions";
        $array = $this->toArray();
        if (array_key_exists($key, $array)) {
            return new StoryVersions($array[$key]);
        }

        throw new StoryblokFormatException(sprintf("Expected '%s' in the response.", $key));
    }
}
