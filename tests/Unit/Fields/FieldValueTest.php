<?php

declare(strict_types=1);

namespace Tests\Unit\Fields;

use Storyblok\ManagementApi\Data\Fields\AssetField;
use Storyblok\ManagementApi\Data\Fields\MultilinkField;
use Storyblok\ManagementApi\Data\Fields\PluginField;
use Storyblok\ManagementApi\Data\Fields\RichtextField;
use Storyblok\ManagementApi\Data\Fields\TableField;
use Storyblok\ManagementApi\Data\StoryComponent;
use Tests\TestCase;

final class FieldValueTest extends TestCase
{
    public function testAssetFieldIsAFieldValue(): void
    {
        $field = new AssetField("https://a.storyblok.com/f/123/image.jpg");

        $this->assertSame("asset", $field->get("fieldtype"));
        $this->assertSame(
            "https://a.storyblok.com/f/123/image.jpg",
            $field->get("filename"),
        );
    }

    public function testMultilinkUrlField(): void
    {
        $field = MultilinkField::url("https://example.com")
            ->setAnchor("pricing")
            ->openInNewTab();

        $this->assertSame("multilink", $field->get("fieldtype"));
        $this->assertSame("url", $field->linktype());
        $this->assertSame("https://example.com", $field->linkUrl());
        $this->assertSame("https://example.com", $field->cachedUrl());
        $this->assertSame("pricing", $field->anchor());
        $this->assertSame("_blank", $field->target());
    }

    public function testMultilinkUrlKeepsCachedUrlInSync(): void
    {
        $field = MultilinkField::url("https://example.com");
        $field->setUrl("https://www.storyblok.com");

        $this->assertSame("https://www.storyblok.com", $field->linkUrl());
        $this->assertSame("https://www.storyblok.com", $field->cachedUrl());
    }

    public function testMultilinkStoryField(): void
    {
        $field = MultilinkField::story(
            "758b3f86-7b4a-4c9a-b06f-7b2e2a6f3d2f",
            "articles/example",
        );

        $this->assertSame("story", $field->linktype());
        $this->assertSame(
            "758b3f86-7b4a-4c9a-b06f-7b2e2a6f3d2f",
            $field->id(),
        );
        $this->assertSame("articles/example", $field->cachedUrl());
    }

    public function testRichtextFieldParagraph(): void
    {
        $field = RichtextField::paragraph("Hello world");

        $this->assertSame("doc", $field->get("type"));
        $this->assertSame("paragraph", $field->get("content.0.type"));
        $this->assertSame("Hello world", $field->get("content.0.content.0.text"));
    }

    public function testRichtextFieldAcceptsRawNodes(): void
    {
        $field = (new RichtextField())->addNode([
            "type" => "horizontal_rule",
        ]);

        $this->assertSame("horizontal_rule", $field->get("content.0.type"));
    }

    public function testTableFieldFromRows(): void
    {
        $field = TableField::fromRows(
            ["Name", "Role"],
            [
                ["Ada", "Engineer"],
                ["Grace", "Scientist"],
            ],
        );

        $this->assertSame("table", $field->get("fieldtype"));
        $this->assertSame("_table_head", $field->get("thead.0.component"));
        $this->assertSame("Name", $field->get("thead.0.value"));
        $this->assertSame("_table_row", $field->get("tbody.0.component"));
        $this->assertSame("_table_col", $field->get("tbody.0.body.0.component"));
        $this->assertSame("Ada", $field->get("tbody.0.body.0.value"));
        $this->assertSame("Scientist", $field->get("tbody.1.body.1.value"));
        $this->assertMatchesRegularExpression(
            "/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/",
            $field->getString("thead.0._uid"),
        );
    }

    public function testTableFieldBuildsStoryblokTableValueShape(): void
    {
        $field = TableField::fromRows(["aaa", "bbbb"], [["xxx", "yyy"]]);

        $this->assertSame("table", $field->get("fieldtype"));
        $this->assertCount(2, $field->thead());
        $this->assertCount(1, $field->tbody());
        $this->assertSame("_table_head", $field->get("thead.0.component"));
        $this->assertSame("aaa", $field->get("thead.0.value"));
        $this->assertSame("_table_head", $field->get("thead.1.component"));
        $this->assertSame("bbbb", $field->get("thead.1.value"));
        $this->assertSame("_table_row", $field->get("tbody.0.component"));
        $this->assertCount(2, $field->getArray("tbody.0.body"));
        $this->assertSame("_table_col", $field->get("tbody.0.body.0.component"));
        $this->assertSame("xxx", $field->get("tbody.0.body.0.value"));
        $this->assertSame("_table_col", $field->get("tbody.0.body.1.component"));
        $this->assertSame("yyy", $field->get("tbody.0.body.1.value"));
    }

    public function testTableFieldAcceptsRawApiShape(): void
    {
        $field = (new TableField())->setThead([
            [
                "_uid" => "head-1",
                "component" => "_table_head",
                "value" => "Name",
            ],
        ]);

        $this->assertSame("head-1", $field->get("thead.0._uid"));
    }

    public function testPluginFieldKeepsCustomPayload(): void
    {
        $field = (new PluginField("custom-plugin", [
            "value" => "custom value",
        ]))->setUid("plugin-uid");

        $this->assertSame("custom-plugin", $field->plugin());
        $this->assertSame("plugin-uid", $field->uid());
        $this->assertSame("custom value", $field->get("value"));
    }

    public function testPluginFieldKeepsPluginFromRawData(): void
    {
        $field = new PluginField(data: [
            "plugin" => "raw-plugin",
            "value" => "custom value",
        ]);

        $this->assertSame("raw-plugin", $field->plugin());
        $this->assertSame("custom value", $field->get("value"));
    }

    public function testStoryComponentSpecializedFieldValueSetters(): void
    {
        $content = new StoryComponent("article-page");
        $content
            ->setMultilink("cta_link", MultilinkField::url("https://example.com")->openInNewTab())
            ->setRichtext("body", RichtextField::paragraph("Hello world"))
            ->setTable("comparison", TableField::fromRows(["Name"], [["Ada"]]))
            ->setPlugin("custom", new PluginField("custom-plugin", ["value" => "ok"]));

        $this->assertSame("multilink", $content->get("cta_link.fieldtype"));
        $this->assertSame("_blank", $content->get("cta_link.target"));
        $this->assertSame("doc", $content->get("body.type"));
        $this->assertSame("Hello world", $content->get("body.content.0.content.0.text"));
        $this->assertSame("_table_head", $content->get("comparison.thead.0.component"));
        $this->assertSame("custom-plugin", $content->get("custom.plugin"));
        $this->assertSame("ok", $content->get("custom.value"));
    }

    public function testStoryComponentRawSetStillWorks(): void
    {
        $content = new StoryComponent("article-page");
        $content->set("cta_link", [
            "fieldtype" => "multilink",
            "linktype" => "url",
            "url" => "https://example.com",
            "id" => "",
            "cached_url" => "",
        ]);

        $this->assertSame("multilink", $content->get("cta_link.fieldtype"));
        $this->assertSame("https://example.com", $content->get("cta_link.url"));
    }
}
