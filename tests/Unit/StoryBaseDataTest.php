<?php

declare(strict_types=1);

namespace Tests\Unit;

use Storyblok\ManagementApi\Data\Story;
use Storyblok\ManagementApi\Data\StoryCollectionItem;
use Storyblok\ManagementApi\Data\StoryComponent;
use Tests\TestCase;

final class StoryBaseDataTest extends TestCase
{
    /**
     * @return mixed[]
     */
    private function decodeJsonFile(string $mockfile): array
    {
        $contentString = $this->mockData($mockfile);
        /** @var mixed[] $content */
        $content = json_decode($contentString, true);

        return $content;
    }

    private function makeStory(): Story
    {
        $content = $this->decodeJsonFile("one-story");
        /** @var mixed[] $story */
        $story = $content["story"];

        return Story::make($story);
    }

    private function makeStoryCollectionItem(): StoryCollectionItem
    {
        $content = $this->decodeJsonFile("list-stories");
        /** @var array<int, mixed[]> $stories */
        $stories = $content["stories"];

        return StoryCollectionItem::make($stories[0]);
    }

    public function testIdOnStory(): void
    {
        $story = $this->makeStory();
        $this->assertSame("440448565", $story->id());
    }

    public function testIdOnStoryCollectionItem(): void
    {
        $item = $this->makeStoryCollectionItem();
        $this->assertSame("440448565", $item->id());
    }

    public function testUuidOnStory(): void
    {
        $story = $this->makeStory();
        $this->assertSame("e656e146-f4ed-44a2-8017-013e5a9d9395", $story->uuid());
    }

    public function testUuidOnStoryCollectionItem(): void
    {
        $item = $this->makeStoryCollectionItem();
        $this->assertSame("e656e146-f4ed-44a2-8017-013e5a9d9396", $item->uuid());
    }

    public function testCreatedAtOnStory(): void
    {
        $story = $this->makeStory();
        $this->assertSame("2024-02-08", $story->createdAt());
        $this->assertSame("2024-02-08 16:26:24", $story->createdAt("Y-m-d H:i:s"));
    }

    public function testCreatedAtOnStoryCollectionItem(): void
    {
        $item = $this->makeStoryCollectionItem();
        $this->assertSame("2024-02-08", $item->createdAt());
    }

    public function testPublishedAtOnStory(): void
    {
        $story = $this->makeStory();
        $this->assertSame("2024-02-08", $story->publishedAt());
        $this->assertSame("2024-02-08 16:27:05", $story->publishedAt("Y-m-d H:i:s"));
    }

    public function testPublishedAtOnStoryCollectionItem(): void
    {
        $item = $this->makeStoryCollectionItem();
        $this->assertSame("2024-02-08", $item->publishedAt());
    }

    public function testUpdatedAtOnStory(): void
    {
        $story = $this->makeStory();
        $this->assertSame("2024-02-08", $story->updatedAt());
        $this->assertSame("2024-02-08 16:27:10", $story->updatedAt("Y-m-d H:i:s"));
    }

    public function testUpdatedAtOnStoryCollectionItem(): void
    {
        $item = $this->makeStoryCollectionItem();
        $this->assertSame("", $item->updatedAt());
    }

    public function testIsValidOnStory(): void
    {
        $story = $this->makeStory();
        $this->assertTrue($story->isValid());
    }

    public function testIsValidOnStoryCollectionItem(): void
    {
        $item = $this->makeStoryCollectionItem();
        $this->assertTrue($item->isValid());
    }

    public function testIsValidFailsWithEmptyName(): void
    {
        $story = new Story("", "some-slug", new StoryComponent("page"));
        $this->assertFalse($story->isValid());
    }

    public function testIsValidFailsWithEmptySlug(): void
    {
        $story = new Story("Some Name", "", new StoryComponent("page"));
        $this->assertFalse($story->isValid());
    }

    public function testFullSlugOnStory(): void
    {
        $story = $this->makeStory();
        $this->assertSame("posts/my-third-post", $story->fullSlug());
    }

    public function testFullSlugOnStoryCollectionItem(): void
    {
        $item = $this->makeStoryCollectionItem();
        $this->assertSame("posts/my-third-post", $item->fullSlug());
    }

    public function testIsStartpageOnStory(): void
    {
        $story = $this->makeStory();
        $this->assertFalse($story->isStartpage());
    }

    public function testIsStartpageOnStoryCollectionItem(): void
    {
        $item = $this->makeStoryCollectionItem();
        $this->assertFalse($item->isStartpage());
    }

    public function testParentIdOnStory(): void
    {
        $story = $this->makeStory();
        $this->assertSame(440448337, $story->parentId());
    }

    public function testParentIdOnStoryCollectionItem(): void
    {
        $item = $this->makeStoryCollectionItem();
        $this->assertSame(440448337, $item->parentId());
    }

    public function testGroupIdOnStory(): void
    {
        $story = $this->makeStory();
        $this->assertSame("b913a671-f1e9-436a-bc5d-2795d2740198", $story->groupId());
    }

    public function testGroupIdOnStoryCollectionItem(): void
    {
        $item = $this->makeStoryCollectionItem();
        $this->assertSame("b913a671-f1e9-436a-bc5d-2795d2740198", $item->groupId());
    }

    public function testReleaseIdOnStory(): void
    {
        $story = $this->makeStory();
        // release_id is null in the mock data
        $this->assertSame(0, $story->releaseId());
    }

    public function testReleaseIdOnStoryCollectionItem(): void
    {
        $item = $this->makeStoryCollectionItem();
        $this->assertSame(0, $item->releaseId());
    }

    public function testFirstPublishedAtOnStory(): void
    {
        $story = $this->makeStory();
        $this->assertSame("2024-02-08", $story->firstPublishedAt());
        $this->assertSame("2024-02-08 16:27:05", $story->firstPublishedAt("Y-m-d H:i:s"));
    }

    public function testFirstPublishedAtOnStoryCollectionItem(): void
    {
        $item = $this->makeStoryCollectionItem();
        $this->assertSame("2024-02-08", $item->firstPublishedAt());
    }

    public function testSlugOnStory(): void
    {
        $story = $this->makeStory();
        $this->assertSame("my-third-post", $story->slug());
    }

    public function testSlugOnStoryCollectionItem(): void
    {
        $item = $this->makeStoryCollectionItem();
        $this->assertSame("my-third-post", $item->slug());
    }

    public function testNameOnStory(): void
    {
        $story = $this->makeStory();
        $this->assertSame("My third post", $story->name());
    }

    public function testNameOnStoryCollectionItem(): void
    {
        $item = $this->makeStoryCollectionItem();
        $this->assertSame("My third post", $item->name());
    }

    public function testHasUnpublishedChangesOnStory(): void
    {
        $story = $this->makeStory();
        $this->assertFalse($story->hasUnpublishedChanges());
    }

    public function testHasUnpublishedChangesTrue(): void
    {
        $item = StoryCollectionItem::make([
            "name" => "Draft story",
            "slug" => "draft-story",
            "unpublished_changes" => true,
        ]);
        $this->assertTrue($item->hasUnpublishedChanges());
    }

    public function testHasUnpublishedChangesDefault(): void
    {
        $item = $this->makeStoryCollectionItem();
        $this->assertFalse($item->hasUnpublishedChanges());
    }

    public function testWorkflowStageIdOnStory(): void
    {
        $story = $this->makeStory();
        $this->assertNull($story->workflowStageId());
    }

    public function testWorkflowStageIdWithValue(): void
    {
        $item = StoryCollectionItem::make([
            "name" => "Reviewed story",
            "slug" => "reviewed-story",
            "stage" => [
                "workflow_stage_id" => 653554,
            ],
        ]);
        $this->assertSame(653554, $item->workflowStageId());
    }

    public function testWorkflowStageIdDefault(): void
    {
        $item = $this->makeStoryCollectionItem();
        $this->assertNull($item->workflowStageId());
    }

    public function testContentTypeWithValue(): void
    {
        $item = StoryCollectionItem::make([
            "name" => "Article story",
            "slug" => "article-story",
            "content_type" => "page",
        ]);
        $this->assertSame("page", $item->contentType());
    }

    public function testContentTypeDefault(): void
    {
        $item = $this->makeStoryCollectionItem();
        $this->assertSame("", $item->contentType());
    }
}
