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
}
