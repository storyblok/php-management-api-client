<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Response;

use Storyblok\ManagementApi\Data\Experiment;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

class ExperimentResponse extends StoryblokResponse implements StoryblokResponseInterface
{
    #[\Override]
    public function data(): Experiment
    {
        $key = "experiment";
        $array = $this->toArray();
        if (array_key_exists($key, $array)) {
            return Experiment::make($array[$key]);
        }

        throw new StoryblokFormatException(sprintf("Expected '%s' in the response.", $key));
    }
}
