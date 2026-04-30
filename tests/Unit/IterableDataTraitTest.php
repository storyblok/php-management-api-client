<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Storyblok\ManagementApi\Data\AssetFolder;
use Storyblok\ManagementApi\Data\AssetFolders;
use Storyblok\ManagementApi\Data\Space;
use Storyblok\ManagementApi\Data\Spaces;
use Storyblok\ManagementApi\Data\Stories;
use Storyblok\ManagementApi\Data\StoryCollectionItem;
use Storyblok\ManagementApi\Data\StoryblokData;
use Tests\TestCase;

/**
 * Verifies that index access ($collection[n]) and foreach iteration
 * return the same typed object — both paths go through IterableDataTrait.
 */
final class IterableDataTraitTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Stories collection
    // -------------------------------------------------------------------------

    #[Test]
    public function stories_index_access_returns_story_collection_item(): void
    {
        $stories = $this->storiesFixture();

        $this->assertInstanceOf(StoryCollectionItem::class, $stories[0]);
    }

    #[Test]
    public function stories_index_access_and_foreach_return_same_type(): void
    {
        $stories = $this->storiesFixture();

        $viaIndex = $stories[0];
        $this->assertInstanceOf(StoryCollectionItem::class, $viaIndex);

        $viaForeach = null;
        foreach ($stories as $item) {
            $this->assertInstanceOf(StoryCollectionItem::class, $item);
            $viaForeach = $item;
            break;
        }

        $this->assertInstanceOf(StoryCollectionItem::class, $viaForeach);
        $this->assertSame($viaIndex->id(), $viaForeach->id());
        $this->assertSame($viaIndex->slug(), $viaForeach->slug());
    }

    #[Test]
    public function stories_index_access_exposes_typed_methods(): void
    {
        $stories = $this->storiesFixture();

        // Before the fix these would throw: StoryblokData has no id()/slug()/name()
        $story = $stories[0];
        $this->assertInstanceOf(StoryCollectionItem::class, $story);
        $this->assertSame('440448565', $story->id());
        $this->assertSame('my-third-post', $story->slug());
        $this->assertSame('My third post', $story->name());
    }

    #[Test]
    public function stories_index_access_supports_array_access_on_returned_item(): void
    {
        $stories = $this->storiesFixture();

        // Array access on the typed item must still work (regression)
        $story = $stories[0];
        $this->assertInstanceOf(StoryCollectionItem::class, $story);
        $this->assertSame(440448565, $story['id']); // JSON integer, no cast needed
        $this->assertSame('my-third-post', $story['slug']);
    }

    #[Test]
    public function stories_second_item_accessible_by_index(): void
    {
        $stories = $this->storiesFixture();

        $second = $stories[1];
        $this->assertInstanceOf(StoryCollectionItem::class, $second);
        // fixture has two identical entries
        $first = $stories[0];
        $this->assertInstanceOf(StoryCollectionItem::class, $first);
        $this->assertSame($first->id(), $second->id());
    }

    // -------------------------------------------------------------------------
    // AssetFolders collection
    // -------------------------------------------------------------------------

    #[Test]
    public function asset_folders_index_access_returns_asset_folder(): void
    {
        $folders = $this->assetFoldersFixture();

        $this->assertInstanceOf(AssetFolder::class, $folders[0]);
    }

    #[Test]
    public function asset_folders_index_access_and_foreach_return_same_type(): void
    {
        $folders = $this->assetFoldersFixture();

        $viaIndex = $folders[0];
        $this->assertInstanceOf(AssetFolder::class, $viaIndex);

        $viaForeach = null;
        foreach ($folders as $item) {
            $this->assertInstanceOf(AssetFolder::class, $item);
            $viaForeach = $item;
            break;
        }

        $this->assertInstanceOf(AssetFolder::class, $viaForeach);
        $this->assertSame($viaIndex->id(), $viaForeach->id());
        $this->assertSame($viaIndex->name(), $viaForeach->name());
    }

    #[Test]
    public function asset_folders_index_access_exposes_typed_methods(): void
    {
        $folders = $this->assetFoldersFixture();

        $folder = $folders[0];
        $this->assertInstanceOf(AssetFolder::class, $folder);
        $this->assertSame('100', $folder->id());
        $this->assertSame('Images', $folder->name());
    }

    // -------------------------------------------------------------------------
    // Spaces collection
    // -------------------------------------------------------------------------

    #[Test]
    public function spaces_index_access_returns_space(): void
    {
        $spaces = $this->spacesFixture();

        $this->assertInstanceOf(Space::class, $spaces[0]);
    }

    #[Test]
    public function spaces_index_access_and_foreach_return_same_type(): void
    {
        $spaces = $this->spacesFixture();

        $viaIndex = $spaces[0];
        $this->assertInstanceOf(Space::class, $viaIndex);

        $viaForeach = null;
        foreach ($spaces as $item) {
            $this->assertInstanceOf(Space::class, $item);
            $viaForeach = $item;
            break;
        }

        $this->assertInstanceOf(Space::class, $viaForeach);
        $this->assertSame($viaIndex->id(), $viaForeach->id());
        $this->assertSame($viaIndex->name(), $viaForeach->name());
    }

    #[Test]
    public function spaces_index_access_exposes_typed_methods(): void
    {
        $spaces = $this->spacesFixture();

        $space = $spaces[0];
        $this->assertInstanceOf(Space::class, $space);
        $this->assertSame('Example Space', $space->name());
        $this->assertSame('680', $space->id());
    }

    // -------------------------------------------------------------------------
    // Regression: scalar field access on single-item data objects
    // -------------------------------------------------------------------------

    #[Test]
    public function scalar_field_access_on_storyblok_data_still_returns_scalar(): void
    {
        // StoryblokData wraps a key-value map; offsetGet on a string key
        // must return the scalar value, not a wrapped object.
        $data = new StoryblokData(['name' => 'Hello', 'id' => 42]);

        $this->assertSame('Hello', $data['name']);
        $this->assertSame(42, $data['id']);
    }

    #[Test]
    public function missing_key_returns_null(): void
    {
        $data = new StoryblokData(['name' => 'Hello']);

        $this->assertNull($data['non_existent']);
    }

    #[Test]
    public function storyblok_data_default_getDataClass_wraps_nested_arrays_in_storyblok_data(): void
    {
        // When getDataClass() returns StoryblokData (the default), nested arrays
        // accessed by index are still wrapped as StoryblokData — same as before.
        $data = new StoryblokData([
            ['id' => 1, 'name' => 'one'],
            ['id' => 2, 'name' => 'two'],
        ]);

        $item = $data[0];
        $this->assertInstanceOf(StoryblokData::class, $item);
        $this->assertSame(1, $item['id']); // PHP array integer, no cast needed
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function storiesFixture(): Stories
    {
        /** @var array{stories: array<mixed>} $raw */
        $raw = json_decode($this->mockData('list-stories'), true);
        return new Stories($raw['stories']);
    }

    private function assetFoldersFixture(): AssetFolders
    {
        /** @var array{asset_folders: array<mixed>} $raw */
        $raw = json_decode($this->mockData('list-asset-folders'), true);
        return new AssetFolders($raw['asset_folders']);
    }

    private function spacesFixture(): Spaces
    {
        /** @var array{spaces: array<mixed>} $raw */
        $raw = json_decode($this->mockData('list-spaces'), true);
        return new Spaces($raw['spaces']);
    }
}
