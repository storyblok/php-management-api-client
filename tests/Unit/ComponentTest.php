<?php

declare(strict_types=1);

namespace Tests\Unit;

use Storyblok\ManagementApi\Data\Component;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldInterface;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldRichtext;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldTable;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldText;
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

    public function testContentTypeNamedConstructorSetsRootOnlyFlags(): void
    {
        $component = Component::contentType("article");

        $this->assertSame("article", $component->name());
        $this->assertTrue($component->isRoot());
        $this->assertTrue($component->isContentType());
        $this->assertFalse($component->isNestable());
        $this->assertFalse($component->isUniversal());
        $this->assertSame([
            "name" => "article",
            "is_root" => true,
            "is_nestable" => false,
        ], $component->toArray());
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

    public function testNestableNamedConstructorSetsNestableOnlyFlags(): void
    {
        $component = Component::nestable("teaser");

        $this->assertSame("teaser", $component->name());
        $this->assertFalse($component->isRoot());
        $this->assertFalse($component->isContentType());
        $this->assertTrue($component->isNestable());
        $this->assertFalse($component->isUniversal());
        $this->assertSame([
            "name" => "teaser",
            "is_root" => false,
            "is_nestable" => true,
        ], $component->toArray());
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

    public function testUniversalNamedConstructorSetsRootAndNestableFlags(): void
    {
        $component = Component::universal("section");

        $this->assertSame("section", $component->name());
        $this->assertTrue($component->isRoot());
        $this->assertFalse($component->isContentType());
        $this->assertTrue($component->isNestable());
        $this->assertTrue($component->isUniversal());
        $this->assertSame([
            "name" => "section",
            "is_root" => true,
            "is_nestable" => true,
        ], $component->toArray());
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

    public function testMetadataSettersAreFluent(): void
    {
        $component = new Component("page");

        $result = $component
            ->setName("article-page")
            ->setDisplayName("Article Page")
            ->setImage("https://example.com/preview.png")
            ->setPreviewField("title")
            ->setRoot()
            ->setNestable(false);

        $this->assertSame($component, $result);
        $this->assertSame("article-page", $component->name());
        $this->assertSame("Article Page", $component->displayName());
        $this->assertSame("https://example.com/preview.png", $component->image());
        $this->assertSame("title", $component->previewField());
        $this->assertTrue($component->isRoot());
        $this->assertFalse($component->isNestable());
    }

    public function testSchemaSettersAreFluent(): void
    {
        $component = new Component("page");
        $schema = [
            "title" => ["type" => "text", "pos" => 0],
        ];

        $result = $component
            ->setSchema($schema)
            ->setField("summary", ["type" => "textarea", "pos" => 1]);

        $this->assertSame($component, $result);
        $this->assertSame("text", $component->getSchema()["title"]["type"]);
        $this->assertSame("textarea", $component->getSchema()["summary"]["type"]);
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

    public function testInsertFieldShiftsExistingEntriesFromPos(): void
    {
        $component = $this->makeComponentWithSchema();
        // before: title=0, body=1, tab-seo=2, meta_title=3, meta_description=4

        $component->insertField(
            (new FieldText("summary"))->setDisplayName("Summary"),
            atPos: 0,
        );

        $fields = $component->getFields();
        $schema = $component->getSchema();

        // new field is at pos 0
        $this->assertSame(0, $fields["summary"]->pos());

        // all previous entries shifted by 1
        $this->assertSame(1, $fields["title"]->pos());
        $this->assertSame(2, $fields["body"]->pos());
        $this->assertSame(4, $fields["meta_title"]->pos());
        $this->assertSame(5, $fields["meta_description"]->pos());

        // tab also shifted
        $this->assertSame(3, $schema["tab-seo"]["pos"]);
    }

    public function testInsertFieldInTheMiddleOnlyShiftsEntriesAtOrAfterPos(): void
    {
        $component = $this->makeComponentWithSchema();
        // before: title=0, body=1, tab-seo=2, meta_title=3, meta_description=4

        $component->insertField(
            (new FieldText("subtitle"))->setDisplayName("Subtitle"),
            atPos: 1,
        );

        $fields = $component->getFields();

        // new field at pos 1
        $this->assertSame(1, $fields["subtitle"]->pos());

        // title before insertion point — unchanged
        $this->assertSame(0, $fields["title"]->pos());

        // everything at pos >= 1 shifted by 1
        $this->assertSame(2, $fields["body"]->pos());
        $this->assertSame(4, $fields["meta_title"]->pos());
        $this->assertSame(5, $fields["meta_description"]->pos());
    }

    public function testInsertFieldLeavesNullPosEntriesUntouched(): void
    {
        $component = Component::make([
            'name'   => 'page',
            'schema' => [
                'title'   => ['type' => 'text', 'pos' => 0],
                'orphan'  => ['type' => 'text'],            // pos key absent
                'orphan2' => ['type' => 'text', 'pos' => null],  // pos key present but null
                'orphan3' => ['type' => 'text', 'pos' => ''],    // pos key present but ""
                'body'    => ['type' => 'richtext', 'pos' => 1],
            ],
        ]);

        $component->insertField(
            (new FieldText("summary"))->setDisplayName("Summary"),
            atPos: 0,
        );

        $schema = $component->getSchema();

        // new field at pos 0
        $this->assertSame(0, $schema["summary"]["pos"]);

        // entries with explicit integer pos are shifted
        $this->assertSame(1, $schema["title"]["pos"]);
        $this->assertSame(2, $schema["body"]["pos"]);

        // pos key absent — stays untouched
        $this->assertArrayNotHasKey("pos", $schema["orphan"]);

        // pos key present but null — stays null
        $this->assertNull($schema["orphan2"]["pos"]);

        // pos key present but "" — stays ""
        $this->assertSame("", $schema["orphan3"]["pos"]);
    }

    public function testInsertFieldIsChainable(): void
    {
        $component = new Component("page");

        $result = $component->insertField(new FieldText("title"), atPos: 0);

        $this->assertSame($component, $result);
    }

    public function testAppendFieldsAddsFieldsInOrder(): void
    {
        $component = new Component("my-component");

        $result = $component->appendFields([
            (new FieldText("title"))->setDisplayName("Title"),
            (new FieldRichtext("body"))->setDisplayName("Body"),
            (new FieldTable("comparison"))->setDisplayName("Comparison"),
        ]);

        $schema = $component->getSchema();

        $this->assertSame($component, $result);
        $this->assertSame(0, $schema["title"]["pos"]);
        $this->assertSame(1, $schema["body"]["pos"]);
        $this->assertSame(2, $schema["comparison"]["pos"]);
        $this->assertSame("text", $schema["title"]["type"]);
        $this->assertSame("richtext", $schema["body"]["type"]);
        $this->assertSame("table", $schema["comparison"]["type"]);
    }

    public function testAppendFieldsContinuesAfterExistingSchemaEntries(): void
    {
        $component = Component::make([
            "name" => "page",
            "schema" => [
                "title" => ["type" => "text", "pos" => 3],
            ],
        ]);

        $component->appendFields([
            new FieldRichtext("body"),
            new FieldTable("comparison"),
        ]);

        $schema = $component->getSchema();

        $this->assertSame(4, $schema["body"]["pos"]);
        $this->assertSame(5, $schema["comparison"]["pos"]);
    }

    public function testAddFieldWithFluentBuilder(): void
    {
        $component = new Component("my-component");
        $component
            ->addField((new FieldText("headline"))->setPos(0)->setRequired())
            ->addField((new FieldRichtext("description"))->setPos(1)->setTranslatable());

        $fields = $component->getFields();

        $this->assertArrayHasKey("headline", $fields);
        $this->assertArrayHasKey("description", $fields);
        $this->assertSame("text", $fields["headline"]->type());
        $this->assertSame("richtext", $fields["description"]->type());
        $this->assertTrue($fields["headline"]->required());
        $this->assertTrue($fields["description"]->translatable());
    }

    public function testAddFieldsAddsFieldsWithoutChangingPos(): void
    {
        $component = new Component("my-component");

        $result = $component->addFields([
            FieldText::make("title")->setDisplayName("Title"),
            FieldRichtext::make("body")->setDisplayName("Body"),
            FieldTable::make("comparison")->setDisplayName("Comparison"),
        ]);

        $schema = $component->getSchema();

        $this->assertSame($component, $result);
        $this->assertArrayNotHasKey("pos", $schema["title"]);
        $this->assertArrayNotHasKey("pos", $schema["body"]);
        $this->assertArrayNotHasKey("pos", $schema["comparison"]);
        $this->assertSame("text", $schema["title"]["type"]);
        $this->assertSame("richtext", $schema["body"]["type"]);
        $this->assertSame("table", $schema["comparison"]["type"]);
    }

    public function testAddFieldsPreservesExplicitPos(): void
    {
        $component = new Component("my-component");

        $component->addFields([
            FieldText::make("title")->setPos(10),
            FieldRichtext::make("body")->setPos(20),
        ]);

        $schema = $component->getSchema();

        $this->assertSame(10, $schema["title"]["pos"]);
        $this->assertSame(20, $schema["body"]["pos"]);
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

    public function testMaxPosReturnsHighestPosAcrossFieldsAndTabs(): void
    {
        // makeComponentWithSchema: title=0, body=1, tab-seo=2, meta_title=3, meta_description=4
        $component = $this->makeComponentWithSchema();

        $this->assertSame(4, $component->maxPos());
    }

    public function testMaxPosReturnsTabPosWhenTabHasHighestPos(): void
    {
        $component = Component::make([
            'name' => 'page',
            'schema' => [
                'title' => ['type' => 'text', 'pos' => 0],
                'body'  => ['type' => 'richtext', 'pos' => 1],
                'tab-style' => [
                    'type' => 'tab',
                    'display_name' => 'Style',
                    'keys' => [],
                    'pos' => 2,
                ],
            ],
        ]);

        $this->assertSame(2, $component->maxPos());
    }

    public function testMaxPosReturnsMinusOneForEmptySchema(): void
    {
        $component = new Component('empty');

        $this->assertSame(-1, $component->maxPos());
    }

    public function testMaxPosAcceptsNumericStringPos(): void
    {
        $component = Component::make([
            'name' => 'page',
            'schema' => [
                'title' => ['type' => 'text', 'pos' => 3],
                'body'  => ['type' => 'richtext', 'pos' => '99'],
            ],
        ]);

        $this->assertSame(99, $component->maxPos());
    }

    public function testMaxPosIgnoresEmptyStringAndNullAndMissingPos(): void
    {
        $component = Component::make([
            'name' => 'page',
            'schema' => [
                'title'   => ['type' => 'text', 'pos' => 3],
                'orphan'  => ['type' => 'text'],
                'orphan2' => ['type' => 'text', 'pos' => null],
                'orphan3' => ['type' => 'text', 'pos' => ''],
            ],
        ]);

        $this->assertSame(3, $component->maxPos());
    }

    public function testMaxPosIgnoresNonNumericStringPos(): void
    {
        $component = Component::make([
            'name' => 'page',
            'schema' => [
                'title'  => ['type' => 'text', 'pos' => 3],
                'broken' => ['type' => 'text', 'pos' => 'aaaa'],
            ],
        ]);

        $this->assertSame(3, $component->maxPos());
    }

    public function testAppendFieldPlacesFieldAfterLastEntry(): void
    {
        // makeComponentWithSchema: title=0, body=1, tab-seo=2, meta_title=3, meta_description=4
        $component = $this->makeComponentWithSchema();

        $component->appendField(new FieldText("footer_note"));

        $this->assertSame(5, $component->getFields()["footer_note"]->pos());
    }

    public function testAppendFieldDoesNotShiftExistingEntries(): void
    {
        $component = $this->makeComponentWithSchema();

        $component->appendField(new FieldText("footer_note"));

        $fields = $component->getFields();
        $this->assertSame(0, $fields["title"]->pos());
        $this->assertSame(1, $fields["body"]->pos());
        $this->assertSame(3, $fields["meta_title"]->pos());
        $this->assertSame(4, $fields["meta_description"]->pos());
    }

    public function testAppendFieldOnEmptySchemaGivesPosZero(): void
    {
        $component = new Component("empty");

        $component->appendField(new FieldText("title"));

        $this->assertSame(0, $component->getFields()["title"]->pos());
    }

    public function testAppendFieldMultipleTimesIncrementsPosSequentially(): void
    {
        $component = new Component("article");

        $component
            ->appendField(new FieldText("title"))
            ->appendField(new FieldText("intro"))
            ->appendField(new FieldRichtext("body"));

        $fields = $component->getFields();
        $this->assertSame(0, $fields["title"]->pos());
        $this->assertSame(1, $fields["intro"]->pos());
        $this->assertSame(2, $fields["body"]->pos());
    }

    public function testAppendFieldIsChainable(): void
    {
        $component = new Component("page");

        $result = $component->appendField(new FieldText("title"));

        $this->assertSame($component, $result);
    }
}
