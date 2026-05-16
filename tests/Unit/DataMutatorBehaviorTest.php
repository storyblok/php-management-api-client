<?php

declare(strict_types=1);

namespace Tests\Unit;

use Storyblok\ManagementApi\Data\ComponentFolder;
use Storyblok\ManagementApi\Data\Space;
use Storyblok\ManagementApi\Data\SpaceEnvironment;
use Storyblok\ManagementApi\Data\Story;
use Storyblok\ManagementApi\Data\StoryblokData;
use Storyblok\ManagementApi\Data\StoryComponent;
use Storyblok\ManagementApi\Data\WorkflowData;
use Storyblok\ManagementApi\Data\WorkflowStageChange;
use Storyblok\ManagementApi\Data\WorkflowStageData;
use Tests\TestCase;

final class DataMutatorBehaviorTest extends TestCase
{
    public function testSetDataKeepsExistingMutationBehavior(): void
    {
        $data = StoryblokData::make(["name" => "Old"]);

        $data->setData(["name" => "New", "slug" => "new"]);

        $this->assertSame(["name" => "New", "slug" => "new"], $data->toArray());
    }

    public function testStorySettersKeepExistingMutationBehavior(): void
    {
        $story = new Story("Old Name", "old-slug", new StoryComponent("page"));
        $content = (new StoryComponent("article"))->set("title", "Updated title");

        $story->setName("New Name");
        $story->setSlug("new-slug");
        $story->setCreatedAt("2026-05-16 10:00:00");
        $story->setContent($content);

        $this->assertSame("New Name", $story->name());
        $this->assertSame("new-slug", $story->slug());
        $this->assertSame(
            "2026-05-16 10:00:00",
            $story->get("created_at"),
        );
        $this->assertSame("article", $story->content()->component());
        $this->assertSame("Updated title", $story->content()->get("title"));
    }

    public function testSpaceSettersAndRemoveDemoModeKeepExistingMutationBehavior(): void
    {
        $space = new Space("Old Name");
        $space->set("is_demo", true);

        $space->setName("New Name");
        $space->setDomain("https://example.com");
        $space->removeDemoMode();

        $this->assertSame("New Name", $space->name());
        $this->assertSame("https://example.com", $space->domain());
        $this->assertFalse($space->isDemo());
    }

    public function testSpaceEnvironmentSettersKeepExistingMutationBehavior(): void
    {
        $environment = new SpaceEnvironment("Old", "https://old.example.com");

        $environment->setName("New");
        $environment->setLocation("https://new.example.com");

        $this->assertSame("New", $environment->name());
        $this->assertSame("https://new.example.com", $environment->location());
    }

    public function testWorkflowStageDataSettersKeepExistingMutationBehavior(): void
    {
        $stage = WorkflowStageData::make([]);

        $stage->setName("Review");
        $stage->setWorkflowId(123);

        $this->assertSame("Review", $stage->name());
        $this->assertSame("123", $stage->workflowId());
    }

    public function testWorkflowDataSettersKeepExistingMutationBehavior(): void
    {
        $workflow = WorkflowData::make([]);

        $workflow->setName("Editorial");

        $this->assertSame("Editorial", $workflow->name());
    }

    public function testWorkflowStageChangeSettersKeepExistingMutationBehavior(): void
    {
        $change = WorkflowStageChange::make([]);

        $change->setStoryAndStage(111, 222);
        $change->setDueDate("2026-05-16");
        $change->setStoryId(333);
        $change->setWorkflowStageId(444);

        $this->assertSame(333, $change->getInt("story_id"));
        $this->assertSame("2026-05-16", $change->get("due_date"));
        $this->assertSame(444, $change->workflowStageId());
    }

    public function testComponentFolderSettersKeepExistingMutationBehavior(): void
    {
        $folder = new ComponentFolder("Old", "0");

        $folder->setName("New");

        $this->assertSame("New", $folder->name());
    }

    public function testDataMutatorsReturnSameInstanceForFluentUsage(): void
    {
        $data = StoryblokData::make(["name" => "Old"]);
        $this->assertSame($data, $data->setData(["name" => "New"]));

        $story = new Story("Old Name", "old-slug", new StoryComponent("page"));
        $this->assertSame(
            $story,
            $story
                ->setName("New Name")
                ->setSlug("new-slug")
                ->setCreatedAt("2026-05-16 10:00:00")
                ->setContent(new StoryComponent("article")),
        );

        $space = new Space("Old Name");
        $this->assertSame(
            $space,
            $space
                ->setName("New Name")
                ->setDomain("https://example.com")
                ->removeDemoMode(),
        );

        $environment = new SpaceEnvironment("Old", "https://old.example.com");
        $this->assertSame(
            $environment,
            $environment
                ->setName("New")
                ->setLocation("https://new.example.com"),
        );

        $stage = WorkflowStageData::make([]);
        $this->assertSame(
            $stage,
            $stage
                ->setName("Review")
                ->setWorkflowId(123),
        );

        $workflow = WorkflowData::make([]);
        $this->assertSame($workflow, $workflow->setName("Editorial"));

        $change = WorkflowStageChange::make([]);
        $this->assertSame(
            $change,
            $change
                ->setStoryAndStage(111, 222)
                ->setDueDate("2026-05-16")
                ->setStoryId(333)
                ->setWorkflowStageId(444),
        );

        $folder = new ComponentFolder("Old", "0");
        $this->assertSame($folder, $folder->setName("New"));
    }
}
