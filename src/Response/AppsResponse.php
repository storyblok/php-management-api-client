<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Response;

use Storyblok\ManagementApi\Data\Apps;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

class AppsResponse extends StoryblokResponse implements StoryblokResponseInterface
{
    #[\Override]
    public function data(): Apps
    {
        $key = "apps";
        $array = $this->toArray();
        if (array_key_exists($key, $array)) {
            return new Apps($array[$key]);
        }

        throw new StoryblokFormatException(sprintf("Expected '%s' in the response.", $key));
    }
}
