<?php

declare(strict_types=1);

namespace Tests\Unit;

use Storyblok\ManagementApi\Data\Component;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldInterface;
use Tests\TestCase;

final class ComponentTest extends TestCase
{
    public function testIsContentType(): void
    {
        $component = Component::make([
            'name' => 'page',
            'is_root' => true,
            'is_nestable' => false,
        ]);

        $this->assertTrue($component->isContentType());
        $this->assertFalse($component->isNestable());
        $this->assertFalse($component->isUniversal());
    }

    public function testIsNestable(): void
    {
        $component = Component::make([
            'name' => 'feature',
            'is_root' => false,
            'is_nestable' => true,
        ]);

        $this->assertTrue($component->isNestable());
        $this->assertFalse($component->isContentType());
        $this->assertFalse($component->isUniversal());
    }

    public function testIsUniversal(): void
    {
        $component = Component::make([
            'name' => 'universal-block',
            'is_root' => true,
            'is_nestable' => true,
        ]);

        $this->assertTrue($component->isUniversal());
        $this->assertTrue($component->isRoot());
        $this->assertTrue($component->isNestable());
        $this->assertFalse($component->isContentType());
    }

    public function testNeitherRootNorNestable(): void
    {
        $component = Component::make([
            'name' => 'plain',
            'is_root' => false,
            'is_nestable' => false,
        ]);

        $this->assertFalse($component->isContentType());
        $this->assertFalse($component->isNestable());
        $this->assertFalse($component->isUniversal());
    }

    public function testGetComponentTypeDetailContentType(): void
    {
        $component = Component::make([
            'name' => 'page',
            'is_root' => true,
            'is_nestable' => false,
        ]);

        $this->assertSame('content-type', $component->getComponentTypeDetail());
    }

    public function testGetComponentTypeDetailNestable(): void
    {
        $component = Component::make([
            'name' => 'feature',
            'is_root' => false,
            'is_nestable' => true,
        ]);

        $this->assertSame('nestable', $component->getComponentTypeDetail());
    }

    public function testGetComponentTypeDetailUniversal(): void
    {
        $component = Component::make([
            'name' => 'universal-block',
            'is_root' => true,
            'is_nestable' => true,
        ]);

        $this->assertSame('universal', $component->getComponentTypeDetail());
    }

    public function testGetComponentTypeDetailEmpty(): void
    {
        $component = Component::make([
            'name' => 'plain',
            'is_root' => false,
            'is_nestable' => false,
        ]);

        $this->assertSame('', $component->getComponentTypeDetail());
    }

    public function testSetNestable(): void
    {
        $component = Component::make([
            'name' => 'block',
            'is_root' => false,
            'is_nestable' => false,
        ]);

        $this->assertFalse($component->isNestable());

        $component->setNestable(true);
        $this->assertTrue($component->isNestable());
    }

    private function makeComponentWithSchema(): Component
    {
        return Component::make([
            'name' => 'page',
            'schema' => [
                'title' => ['type' => 'text', 'pos' => 0],
                'body'  => ['type' => 'richtext', 'pos' => 1],
                'tab-seo' => [
                    'type' => 'tab',
                    'display_name' => 'SEO',
                    'keys' => ['meta_title', 'meta_description'],
                    'pos' => 2,
                ],
                'meta_title'       => ['type' => 'text', 'pos' => 3],
                'meta_description' => ['type' => 'textarea', 'pos' => 4],
            ],
        ]);
    }

    public function testGetFieldsReturnsOnlyNonTabEntries(): void
    {
        $component = $this->makeComponentWithSchema();
        $fields = $component->getFields();

        $this->assertArrayHasKey('title', $fields);
        $this->assertArrayHasKey('body', $fields);
        $this->assertArrayHasKey('meta_title', $fields);
        $this->assertArrayHasKey('meta_description', $fields);
        $this->assertArrayNotHasKey('tab-seo', $fields);
    }

    public function testGetFieldsReturnsFieldInterfaceObjects(): void
    {
        $component = $this->makeComponentWithSchema();

        foreach ($component->getFields() as $key => $field) {
            $this->assertInstanceOf(FieldInterface::class, $field);
            $this->assertSame($key, $field->key());
        }
    }

    public function testGetFieldsIsSortedByPos(): void
    {
        $component = $this->makeComponentWithSchema();
        $keys = array_keys($component->getFields());

        $this->assertSame(['title', 'body', 'meta_title', 'meta_description'], $keys);
    }

    public function testGetFieldAccessors(): void
    {
        $component = $this->makeComponentWithSchema();
        $fields = $component->getFields();

        $this->assertSame('title', $fields['title']->key());
        $this->assertSame('text', $fields['title']->type());
        $this->assertSame(0, $fields['title']->pos());
        $this->assertSame('richtext', $fields['body']->type());
        $this->assertSame(1, $fields['body']->pos());
    }

    public function testSharedFieldProperties(): void
    {
        $component = Component::make([
            'name' => 'article',
            'schema' => [
                'title' => [
                    'type'         => 'text',
                    'pos'          => 0,
                    'required'     => true,
                    'translatable' => true,
                    'no_translate' => false,
                    'description'  => 'The article title',
                    'tooltip'      => true,
                ],
            ],
        ]);

        $field = $component->getFields()['title'];

        $this->assertTrue($field->required());
        $this->assertTrue($field->translatable());
        $this->assertFalse($field->noTranslate());
        $this->assertSame('The article title', $field->description());
        $this->assertTrue($field->tooltip());
    }

    public function testSharedFieldPropertiesDefaults(): void
    {
        $component = Component::make([
            'name' => 'simple',
            'schema' => [
                'title' => ['type' => 'text', 'pos' => 0],
            ],
        ]);

        $field = $component->getFields()['title'];

        $this->assertFalse($field->required());
        $this->assertFalse($field->translatable());
        $this->assertFalse($field->noTranslate());
        $this->assertSame('', $field->description());
        $this->assertFalse($field->tooltip());
    }

    public function testGetFieldsFilteredByTab(): void
    {
        $component = $this->makeComponentWithSchema();
        $fields = $component->getFields('SEO');

        $this->assertArrayHasKey('meta_title', $fields);
        $this->assertArrayHasKey('meta_description', $fields);
        $this->assertArrayNotHasKey('title', $fields);
        $this->assertArrayNotHasKey('body', $fields);
    }

    public function testGetFieldsFilteredByUnknownTabReturnsEmpty(): void
    {
        $component = $this->makeComponentWithSchema();

        $this->assertCount(0, $component->getFields('Nonexistent'));
    }

    public function testGetTabsReturnsOnlyTabEntries(): void
    {
        $component = $this->makeComponentWithSchema();
        $tabs = $component->getTabs();

        $this->assertCount(1, $tabs);
        $this->assertArrayHasKey('tab-seo', $tabs);
        $this->assertSame('SEO', $tabs['tab-seo']['display_name']);
    }

    public function testGetTabsIsSortedByPos(): void
    {
        $component = Component::make([
            'name' => 'page',
            'schema' => [
                'tab-advanced' => ['type' => 'tab', 'display_name' => 'Advanced', 'keys' => ['slug'], 'pos' => 5],
                'tab-seo'      => ['type' => 'tab', 'display_name' => 'SEO', 'keys' => ['meta_title'], 'pos' => 3],
                'tab-content'  => ['type' => 'tab', 'display_name' => 'Content', 'keys' => ['title'], 'pos' => 1],
            ],
        ]);

        $tabNames = array_map(fn(array $t) => $t['display_name'], $component->getTabs());
        $this->assertSame(['Content', 'SEO', 'Advanced'], array_values($tabNames));
    }

    public function testGetTabsIsEmptyWhenNoTabs(): void
    {
        $component = Component::make([
            'name' => 'simple',
            'schema' => [
                'title' => ['type' => 'text', 'pos' => 0],
            ],
        ]);

        $this->assertCount(0, $component->getTabs());
    }

    public function testGetFieldTabReturnsTabDisplayName(): void
    {
        $component = $this->makeComponentWithSchema();

        $this->assertSame('SEO', $component->getFieldTab('meta_title'));
        $this->assertSame('SEO', $component->getFieldTab('meta_description'));
    }

    public function testGetFieldTabReturnsNullForFieldNotInAnyTab(): void
    {
        $component = $this->makeComponentWithSchema();

        $this->assertNull($component->getFieldTab('title'));
        $this->assertNull($component->getFieldTab('body'));
    }

    public function testGetFieldTabReturnsNullForUnknownField(): void
    {
        $component = $this->makeComponentWithSchema();

        $this->assertNull($component->getFieldTab('nonexistent'));
    }
}
