<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Storyblok\ManagementApi\QueryParameters\StoriesParams;
use Tests\TestCase;

final class StoriesParamsTest extends TestCase
{
    #[Test]
    public function empty_params_produce_empty_array(): void
    {
        $params = new StoriesParams();
        $this->assertSame([], $params->toArray());
    }

    #[Test]
    public function with_parent_int_is_sent_to_api(): void
    {
        $params = new StoriesParams(withParent: 12345);
        $this->assertSame(['with_parent' => 12345], $params->toArray());
    }

    #[Test]
    public function with_parent_null_is_excluded_from_array(): void
    {
        $params = new StoriesParams(withParent: null);
        $this->assertArrayNotHasKey('with_parent', $params->toArray());
    }

    #[Test]
    public function with_parent_zero_is_sent_to_api_unchanged(): void
    {
        // withParent: 0 IS included in the query params, but the Storyblok API
        // treats 0 as falsy and ignores it — use client-side filtering instead.
        $params = new StoriesParams(withParent: 0);
        $this->assertSame(['with_parent' => 0], $params->toArray());
    }

    #[Test]
    public function in_release_int_is_included(): void
    {
        $params = new StoriesParams(inRelease: 999);
        $this->assertSame(['in_release' => 999], $params->toArray());
    }

    #[Test]
    public function in_release_null_is_excluded(): void
    {
        $params = new StoriesParams(inRelease: null);
        $this->assertArrayNotHasKey('in_release', $params->toArray());
    }

    #[Test]
    public function story_only_flag_is_serialized(): void
    {
        $params = new StoriesParams(storyOnly: true);
        $this->assertSame(['story_only' => '1'], $params->toArray());
    }

    #[Test]
    public function folder_only_flag_is_serialized(): void
    {
        $params = new StoriesParams(folderOnly: true);
        $this->assertSame(['folder_only' => 'true'], $params->toArray());
    }

    #[Test]
    public function with_tag_string_is_passed_through(): void
    {
        $params = new StoriesParams(withTag: 'featured');
        $this->assertSame(['with_tag' => 'featured'], $params->toArray());
    }

    #[Test]
    public function with_tag_array_is_comma_joined(): void
    {
        $params = new StoriesParams(withTag: ['featured', 'news']);
        $this->assertSame(['with_tag' => 'featured,news'], $params->toArray());
    }

    #[Test]
    public function starts_with_is_included(): void
    {
        $params = new StoriesParams(startsWith: 'articles/');
        $this->assertSame(['starts_with' => 'articles/'], $params->toArray());
    }

    #[Test]
    public function search_is_included(): void
    {
        $params = new StoriesParams(search: 'hello');
        $this->assertSame(['search' => 'hello'], $params->toArray());
    }

    #[Test]
    public function multiple_params_combine_correctly(): void
    {
        $params = new StoriesParams(
            storyOnly: true,
            withParent: 456,
            startsWith: 'blog/',
        );
        $result = $params->toArray();

        $this->assertSame('1', $result['story_only']);
        $this->assertSame(456, $result['with_parent']);
        $this->assertSame('blog/', $result['starts_with']);
        $this->assertCount(3, $result);
    }
}
