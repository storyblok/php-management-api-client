<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Data\StoryblokData;
use Storyblok\ManagementApi\StoryblokUtils;

class WorkflowStageData extends StoryblokData
{
    /**
     * @param array<string, array<mixed>> $data
     */
    public static function makeFromResponse(array $data = []): self
    {
        return new self($data["workflow_stage"] ?? []);
    }

    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    public function setName(string $name): self
    {
        $this->set('name', $name);
        return $this;
    }

    public function setWorkflowId(string|int $workflowId): self
    {
        $this->set('workflow_id', $workflowId);
        return $this;
    }

    public function name(): string
    {
        return $this->getString('name', "");
    }

    public function id(): string
    {
        return $this->getString('id', "");
    }

    public function color(): string
    {
        return $this->getString("color", "");
    }

    public function workflowId(): string
    {
        return $this->getString("workflow_id", "");
    }
}
