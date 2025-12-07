<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

class WorkflowStageChanges extends StoryblokData
{
    #[\Override]
    public function getDataClass(): string
    {
        return WorkflowStageChange::class;
    }

    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    /**
     * @param array<string, array<mixed>> $data
     */
    public static function makeFromResponse(array $data = []): self
    {
        return new self($data["workflow_stage_changes"] ?? []);
    }

    public function howManyWorkflowStageChanges(): int
    {
        return $this->count();
    }
}
