<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

abstract class StoryBaseData extends BaseData
{
    public function slug(): string
    {
        return $this->getString('slug');
    }

    public function hasWorkflowStage(): bool
    {
        $workflowStageId = $this->getInt('stage.workflow_stage_id', 0);
        return $workflowStageId > 0;
    }

    public function hasTags(): bool
    {
        $tags = $this->getArray('tag_list', []);
        return $tags !== [];
    }

    public function tagListAsString(): string
    {
        $tags = $this->getArray('tag_list', []);
        return implode(", ", $tags);
    }

    /**
     * Returns the list of tags as array.
     *
     * @return array<mixed>
     */
    public function tagListAsArray(): array
    {
        return $this->getArray('tag_list', []);
    }
}
