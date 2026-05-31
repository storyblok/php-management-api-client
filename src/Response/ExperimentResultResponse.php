<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Response;

use Storyblok\ManagementApi\Data\ExperimentResult;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

class ExperimentResultResponse extends StoryblokResponse implements StoryblokResponseInterface
{
    #[\Override]
    public function data(): ExperimentResult
    {
        $key = "experiment_result";
        $array = $this->toArray();
        if (array_key_exists($key, $array)) {
            return ExperimentResult::make($array[$key]);
        }

        throw new StoryblokFormatException(sprintf("Expected '%s' in the response.", $key));
    }
}
