<?php

declare(strict_types=1);

namespace Tests\Unit;

use Storyblok\ManagementApi\Data\WorkflowStageData;
use Tests\TestCase;

final class WorkflowStageDataTest extends TestCase
{
    private function makeWorkflowStage(): WorkflowStageData
    {
        $contentString = $this->mockData("one-workflow-stage");
        /** @var array<string, array<mixed>> $content */
        $content = json_decode($contentString, true);

        return WorkflowStageData::makeFromResponse($content);
    }

    public function testColorWithValue(): void
    {
        $stage = $this->makeWorkflowStage();
        $this->assertSame("#babcb6", $stage->color());
    }

    public function testColorDefault(): void
    {
        $stage = WorkflowStageData::make([]);
        $this->assertSame("", $stage->color());
    }

    public function testWorkflowIdWithValue(): void
    {
        $stage = $this->makeWorkflowStage();
        $this->assertSame("93606", $stage->workflowId());
    }

    public function testWorkflowIdDefault(): void
    {
        $stage = WorkflowStageData::make([]);
        $this->assertSame("", $stage->workflowId());
    }
}
