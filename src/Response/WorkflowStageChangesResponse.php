<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Response;

use Storyblok\ManagementApi\Data\WorkflowStageChanges;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;
use Storyblok\ManagementApi\Response\StoryblokResponse;

class WorkflowStageChangesResponse extends StoryblokResponse implements
    StoryblokResponseInterface
{
    #[\Override]
    public function data(): WorkflowStageChanges
    {
        $key = "workflow_stage_changes";
        $array = $this->toArray();
        if (array_key_exists($key, $array)) {
            return new WorkflowStageChanges($array[$key]);
        }

        throw new StoryblokFormatException(
            sprintf("Expected '%s' in the response.", $key),
        );
    }
}
