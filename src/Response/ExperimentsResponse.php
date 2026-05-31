<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Response;

use Storyblok\ManagementApi\Data\Experiments;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

class ExperimentsResponse extends StoryblokResponse implements StoryblokResponseInterface
{
    #[\Override]
    public function data(): Experiments
    {
        $key = "experiments";
        $array = $this->toArray();
        if (array_key_exists($key, $array) && is_array($array[$key])) {
            return Experiments::make($array[$key]);
        }

        throw new StoryblokFormatException(sprintf("Expected '%s' in the response.", $key));
    }
}
