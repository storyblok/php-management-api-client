<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Response;

use Storyblok\ManagementApi\Data\AppProvisions;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

class AppProvisionsResponse extends StoryblokResponse implements StoryblokResponseInterface
{
    #[\Override]
    public function data(): AppProvisions
    {
        $key = "app_provisions";
        $array = $this->toArray();
        if (array_key_exists($key, $array)) {
            return new AppProvisions($array[$key]);
        }

        throw new StoryblokFormatException(sprintf("Expected '%s' in the response.", $key));
    }
}
