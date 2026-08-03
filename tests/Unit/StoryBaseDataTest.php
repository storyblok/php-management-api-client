<?php

declare(strict_types=1);

namespace Tests\Unit;

use Storyblok\ManagementApi\Data\LocalizedPathData;
use Storyblok\ManagementApi\Data\Story;
use Storyblok\ManagementApi\Data\StoryCollectionItem;
use Storyblok\ManagementApi\Data\StoryComponent;
use Storyblok\ManagementApi\Data\TranslatedSlug;
use Storyblok\ManagementApi\Data\TranslatedSlugData;
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

    public function testSetContentFieldSetsFieldInsideContentPayload(): void
    {
        $story = new Story("Article", "article", StoryComponent::makeComponent("article-page"));

        $result = $story
            ->setContentField("headline", "Updated headline")
            ->setContentField("categories", ["category-uuid"]);

        $this->assertSame($story, $result);
        $this->assertSame("Updated headline", $story->get("content.headline"));
        $this->assertSame(["category-uuid"], $story->getArray("content.categories"));
        $this->assertSame("Updated headline", $story->getContentField("headline"));
        $this->assertSame(["category-uuid"], $story->getContentField("categories"));
    }

    public function testContentFieldHelpersSupportNestedContentPaths(): void
    {
        $story = new Story("Article", "article", StoryComponent::makeComponent("article-page"));

        $story->setContentField("seo.title", "SEO title");

        $this->assertSame("SEO title", $story->get("content.seo.title"));
        $this->assertSame("SEO title", $story->getContentField("seo.title"));
    }

    public function testContentFieldHelpersSupportTranslatedFieldValues(): void
    {
        $story = new Story("Article", "article", StoryComponent::makeComponent("article-page"));

        $result = $story
            ->setContentField("headline", "Default headline")
            ->setContentField("headline", "Deutsche Uberschrift", "de");

        $this->assertSame($story, $result);
        $this->assertSame("Default headline", $story->get("content.headline"));
        $this->assertSame("Deutsche Uberschrift", $story->get("content.headline__i18n__de"));
        $this->assertSame("Default headline", $story->getContentField("headline"));
        $this->assertSame("Deutsche Uberschrift", $story->getContentField("headline", language: "de"));
    }

    public function testTranslatedContentFieldGetterSupportsDefaults(): void
    {
        $story = new Story("Article", "article", StoryComponent::makeComponent("article-page"));

        $this->assertSame(
            "fallback",
            $story->getContentField("headline", "fallback", "de"),
        );
    }

    public function testGetContentFieldReturnsDefaultWhenMissing(): void
    {
        $story = new Story("Article", "article", StoryComponent::makeComponent("article-page"));

        $this->assertSame("fallback", $story->getContentField("missing", "fallback"));
    }

    public function testAsFolderWithMinimumFieldsAutoGeneratesSlug(): void
    {
        $folder = Story::asFolder("My Folder");

        $this->assertTrue($folder->isValid());
        $this->assertSame("My Folder", $folder->name());
        $this->assertSame("my-folder", $folder->slug());
        $this->assertTrue($folder->isFolder());
        $this->assertSame(0, $folder->parentId());
    }

    public function testAsFolderRespectsExplicitSlug(): void
    {
        $folder = Story::asFolder("My Folder", "custom-slug");

        $this->assertSame("custom-slug", $folder->slug());
    }

    public function testAsFolderWithAllFieldsProducesExpectedPayload(): void
    {
        $folder = Story::asFolder(
            name: "Blog",
            parentId: 42,
            defaultContentType: "page",
            contentTypes: ["page", "post"],
            lockSubfoldersContentTypes: true,
            disableFeEditor: true,
        );

        $this->assertSame("Blog", $folder->name());
        $this->assertSame("blog", $folder->slug());
        $this->assertTrue($folder->isFolder());
        $this->assertSame(42, $folder->parentId());
        $this->assertSame("page", $folder->getString("default_root"));
        $this->assertTrue($folder->getBoolean("disable_fe_editor"));

        $content = $folder->getArray("content");
        $this->assertSame(["page", "post"], $content["content_types"]);
        $this->assertTrue($content["lock_subfolders_content_types"]);
    }

    public function testAsFolderWithoutContentTypesOmitsRestrictionFields(): void
    {
        $folder = Story::asFolder("Empty Restrictions");

        $content = $folder->getArray("content");
        $this->assertArrayNotHasKey("content_types", $content);
        $this->assertArrayNotHasKey("lock_subfolders_content_types", $content);
    }

    public function testTranslatedSlugCreatePayloadOmitsId(): void
    {
        $translatedSlug = TranslatedSlug::create(
            lang: "de",
            slug: "mein-artikel",
            name: "Mein Artikel",
            published: true,
        );

        $this->assertSame(
            [
                "lang"      => "de",
                "slug"      => "mein-artikel",
                "name"      => "Mein Artikel",
                "published" => true,
            ],
            $translatedSlug->toArray(),
        );
    }

    public function testTranslatedSlugUpdatePayloadIncludesId(): void
    {
        $translatedSlug = TranslatedSlug::update(
            id: 123,
            slug: "mein-artikel-neu",
            name: "Mein Artikel Neu",
        );

        $this->assertSame(
            [
                "id"   => 123,
                "slug" => "mein-artikel-neu",
                "name" => "Mein Artikel Neu",
            ],
            $translatedSlug->toArray(),
        );
    }

    public function testTranslatedSlugDeletePayloadIncludesDestroyFlag(): void
    {
        $translatedSlug = TranslatedSlug::delete(123);

        $this->assertSame(
            [
                "id"       => 123,
                "_destroy" => true,
            ],
            $translatedSlug->toArray(),
        );
    }

    public function testStorySetsTranslatedSlugAttributes(): void
    {
        $story = new Story("Article", "article", StoryComponent::makeComponent("article-page"));

        $result = $story->setTranslatedSlugsAttributes([
            TranslatedSlug::create("de", "mein-artikel", "Mein Artikel"),
            [
                "id"       => 123,
                "_destroy" => true,
            ],
        ]);

        $this->assertSame($story, $result);
        $this->assertSame(
            [
                [
                    "lang" => "de",
                    "slug" => "mein-artikel",
                    "name" => "Mein Artikel",
                ],
                [
                    "id"       => 123,
                    "_destroy" => true,
                ],
            ],
            $story->getArray("translated_slugs_attributes"),
        );
    }

    public function testStoryAddsTranslatedSlugAttributes(): void
    {
        $story = new Story("Article", "article", StoryComponent::makeComponent("article-page"));

        $result = $story
            ->addTranslatedSlug(TranslatedSlug::create("de", "mein-artikel"))
            ->addTranslatedSlug(TranslatedSlug::delete(123));

        $this->assertSame($story, $result);
        $this->assertSame(
            [
                [
                    "lang" => "de",
                    "slug" => "mein-artikel",
                ],
                [
                    "id"       => 123,
                    "_destroy" => true,
                ],
            ],
            $story->getArray("translated_slugs_attributes"),
        );
    }

    public function testStoryReadsTranslatedSlugsAndLocalizedPaths(): void
    {
        $story = $this->makeStory();
        $translatedSlugs = $story->translatedSlugs();
        $localizedPaths = $story->localizedPaths();

        $this->assertCount(2, $translatedSlugs);
        $this->assertSame(
            [
                [
                    "path"      => "posts/my-third-post",
                    "name"      => null,
                    "lang"      => "fr",
                    "published" => null,
                ],
                [
                    "path"      => "posts/mein-dritter-beitrag",
                    "name"      => "Mein dritter Beitrag",
                    "lang"      => "de",
                    "published" => true,
                ],
            ],
            $translatedSlugs->toArray(),
        );
        $this->assertSame([], $localizedPaths->toArray());

        $firstTranslatedSlug = $translatedSlugs[0];
        $this->assertInstanceOf(TranslatedSlugData::class, $firstTranslatedSlug);
        $this->assertSame("fr", $firstTranslatedSlug->lang());
        $this->assertSame("posts/my-third-post", $firstTranslatedSlug->path());
        $this->assertSame("", $firstTranslatedSlug->slug());
        $this->assertSame("", $firstTranslatedSlug->name());
        $this->assertFalse($firstTranslatedSlug->published());

        $secondTranslatedSlug = $translatedSlugs[1];
        $this->assertInstanceOf(TranslatedSlugData::class, $secondTranslatedSlug);
        $this->assertSame("de", $secondTranslatedSlug->lang());
        $this->assertSame("posts/mein-dritter-beitrag", $secondTranslatedSlug->path());
        $this->assertSame("Mein dritter Beitrag", $secondTranslatedSlug->name());
        $this->assertTrue($secondTranslatedSlug->published());
    }

    public function testStoryReadsLocalizedPathData(): void
    {
        $story = new Story("Article", "article", StoryComponent::makeComponent("article-page"));
        $story->setData([
            "name"            => "Article",
            "slug"            => "article",
            "content"         => ["component" => "article-page"],
            "localized_paths" => [
                [
                    "path"      => "artikel/mein-artikel",
                    "name"      => "Mein Artikel",
                    "lang"      => "de",
                    "published" => true,
                ],
            ],
        ]);

        $localizedPaths = $story->localizedPaths();
        $this->assertSame(
            [
                [
                    "path"      => "artikel/mein-artikel",
                    "name"      => "Mein Artikel",
                    "lang"      => "de",
                    "published" => true,
                ],
            ],
            $localizedPaths->toArray(),
        );

        $localizedPath = $localizedPaths[0];
        $this->assertInstanceOf(LocalizedPathData::class, $localizedPath);
        $this->assertSame("artikel/mein-artikel", $localizedPath->path());
        $this->assertSame("Mein Artikel", $localizedPath->name());
        $this->assertSame("de", $localizedPath->lang());
        $this->assertTrue($localizedPath->published());
    }
}
