<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Data\StoryblokData;

class WorkflowStageChange extends StoryblokData
{
    /**
     * @param array<string, array<mixed>> $data
     */
    public static function makeFromResponse(array $data = []): self
    {
        return new self($data["workflow_stage_change"] ?? []);
    }

    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    public static function makeFromParams(
        int $storyId,
        int $workflowStageId,
        ?string $dueDate = null,
    ): self {
        $change = new self();
        $change->setStoryAndStage($storyId, $workflowStageId);
        if (!is_null($dueDate)) {
            $change->setDueDate($dueDate);
        }

        return $change;
    }

    public function setStoryAndStage(int $storyId, int $workflowStageId): void
    {
        $this->setStoryId($storyId);
        $this->setWorkflowStageId($workflowStageId);
    }

    public function setStoryId(int $storyId): void
    {
        $this->set("story_id", $storyId);
    }

    public function setDueDate(string $dueDate): void
    {
        $this->set("due_date", $dueDate);
    }

    public function setWorkflowStageId(int $workflowStageId): void
    {
        $this->set("workflow_stage_id", $workflowStageId);
    }

    public function id(): ?int
    {
        return $this->getInt("id");
    }

    public function workflowStageId(): ?int
    {
        return $this->getInt("workflow_stage_id");
    }

    public function userId(): ?int
    {
        return $this->getInt("user_id");
    }
}
