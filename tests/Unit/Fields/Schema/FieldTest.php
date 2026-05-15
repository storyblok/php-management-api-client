<?php

declare(strict_types=1);

namespace Tests\Unit\Fields\Schema;

use Storyblok\ManagementApi\Data\Fields\Schema\FieldAsset;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldBloks;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldBoolean;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldDatetime;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldGeneric;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldMarkdown;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldMultilink;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldMultiasset;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldNumber;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldOption;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldOptions;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldPlugin;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldRichtext;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldSection;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldTable;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldText;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldTextarea;
use Tests\TestCase;

final class FieldTest extends TestCase
{
    public function testFactoryReturnsCorrectTypes(): void
    {
        $this->assertInstanceOf(FieldText::class, FieldGeneric::make("f", ["type" => "text"]));
        $this->assertInstanceOf(FieldTextarea::class, FieldGeneric::make("f", ["type" => "textarea"]));
        $this->assertInstanceOf(FieldMarkdown::class, FieldGeneric::make("f", ["type" => "markdown"]));
        $this->assertInstanceOf(FieldDatetime::class, FieldGeneric::make("f", ["type" => "datetime"]));
        $this->assertInstanceOf(FieldOption::class, FieldGeneric::make("f", ["type" => "option"]));
        $this->assertInstanceOf(FieldOptions::class, FieldGeneric::make("f", ["type" => "options"]));
        $this->assertInstanceOf(FieldBloks::class, FieldGeneric::make("f", ["type" => "bloks"]));
        $this->assertInstanceOf(FieldNumber::class, FieldGeneric::make("f", ["type" => "number"]));
        $this->assertInstanceOf(FieldBoolean::class, FieldGeneric::make("f", ["type" => "boolean"]));
        $this->assertInstanceOf(FieldAsset::class, FieldGeneric::make("f", ["type" => "asset"]));
        $this->assertInstanceOf(FieldMultiasset::class, FieldGeneric::make("f", ["type" => "multiasset"]));
        $this->assertInstanceOf(FieldMultilink::class, FieldGeneric::make("f", ["type" => "multilink"]));
        $this->assertInstanceOf(FieldRichtext::class, FieldGeneric::make("f", ["type" => "richtext"]));
        $this->assertInstanceOf(FieldTable::class, FieldGeneric::make("f", ["type" => "table"]));
        $this->assertInstanceOf(FieldPlugin::class, FieldGeneric::make("f", ["type" => "custom"]));
        $this->assertInstanceOf(FieldPlugin::class, FieldGeneric::make("f", ["type" => "plugin"]));
        $this->assertInstanceOf(FieldSection::class, FieldGeneric::make("f", ["type" => "section"]));
        $this->assertInstanceOf(FieldGeneric::class, FieldGeneric::make("f", ["type" => "unknown"]));
    }

    public function testFieldText(): void
    {
        $field = FieldGeneric::make("title", [
            "type"          => "text",
            "pos"           => 0,
            "default_value" => "Hello",
            "regex"         => "^[A-Z].*",
        ]);

        $this->assertInstanceOf(FieldText::class, $field);
        $this->assertSame("Hello", $field->defaultValue());
        $this->assertSame("^[A-Z].*", $field->regex());
    }

    public function testFieldTextDefaults(): void
    {
        $field = FieldGeneric::make("title", ["type" => "text", "pos" => 0]);
        $this->assertInstanceOf(FieldText::class, $field);

        $this->assertSame("", $field->defaultValue());
        $this->assertSame("", $field->regex());
    }

    public function testFieldTextarea(): void
    {
        $field = FieldGeneric::make("summary", [
            "type"          => "textarea",
            "pos"           => 0,
            "default_value" => "Hello",
            "regex"         => "^[A-Z].*",
        ]);

        $this->assertInstanceOf(FieldTextarea::class, $field);
        $this->assertSame("Hello", $field->defaultValue());
        $this->assertSame("^[A-Z].*", $field->regex());
    }

    public function testFieldMarkdown(): void
    {
        $field = FieldGeneric::make("body", [
            "type"          => "markdown",
            "pos"           => 0,
            "default_value" => "# Hello",
        ]);

        $this->assertInstanceOf(FieldMarkdown::class, $field);
        $this->assertSame("# Hello", $field->defaultValue());
    }

    public function testFieldDatetime(): void
    {
        $field = FieldGeneric::make("starts_at", [
            "type"          => "datetime",
            "pos"           => 0,
            "default_value" => "2026-05-14 10:00",
        ]);

        $this->assertInstanceOf(FieldDatetime::class, $field);
        $this->assertSame("2026-05-14 10:00", $field->defaultValue());
    }

    public function testFieldOption(): void
    {
        $options = [["name" => "News", "value" => "news"]];
        $field = FieldGeneric::make("category", [
            "type"            => "option",
            "pos"             => 0,
            "options"         => $options,
            "source"          => "internal",
            "datasource_slug" => "categories",
            "default_value"   => "news",
        ]);

        $this->assertInstanceOf(FieldOption::class, $field);
        $this->assertSame($options, $field->options());
        $this->assertSame("internal", $field->source());
        $this->assertSame("categories", $field->datasourceSlug());
        $this->assertSame("news", $field->defaultValue());
    }

    public function testFieldOptions(): void
    {
        $options = [
            ["name" => "News", "value" => "news"],
            ["name" => "Tech", "value" => "tech"],
        ];
        $field = FieldGeneric::make("categories", [
            "type"            => "options",
            "pos"             => 0,
            "options"         => $options,
            "source"          => "internal",
            "datasource_slug" => "categories",
        ]);

        $this->assertInstanceOf(FieldOptions::class, $field);
        $this->assertSame($options, $field->options());
        $this->assertSame("internal", $field->source());
        $this->assertSame("categories", $field->datasourceSlug());
    }

    public function testFieldNumber(): void
    {
        $field = FieldGeneric::make("score", [
            "type"          => "number",
            "pos"           => 0,
            "default_value" => "5",
            "min_value"     => 1,
            "max_value"     => 100,
        ]);

        $this->assertInstanceOf(FieldNumber::class, $field);
        $this->assertSame("5", $field->defaultValue());
        $this->assertSame(1, $field->minValue());
        $this->assertSame(100, $field->maxValue());
    }

    public function testFieldNumberNullableBounds(): void
    {
        $field = FieldGeneric::make("score", ["type" => "number", "pos" => 0]);
        $this->assertInstanceOf(FieldNumber::class, $field);

        $this->assertNull($field->minValue());
        $this->assertNull($field->maxValue());
    }

    public function testFieldBoolean(): void
    {
        $field = FieldGeneric::make("active", [
            "type"           => "boolean",
            "pos"            => 0,
            "default_value"  => true,
            "inline_label"   => true,
            "checkbox_label" => "Is active",
        ]);

        $this->assertInstanceOf(FieldBoolean::class, $field);
        $this->assertTrue($field->defaultValue());
        $this->assertTrue($field->inlineLabel());
        $this->assertSame("Is active", $field->checkboxLabel());
    }

    public function testFieldBooleanDefaults(): void
    {
        $field = FieldGeneric::make("active", ["type" => "boolean", "pos" => 0]);
        $this->assertInstanceOf(FieldBoolean::class, $field);

        $this->assertFalse($field->defaultValue());
        $this->assertFalse($field->inlineLabel());
        $this->assertSame("", $field->checkboxLabel());
    }

    public function testFieldRichtext(): void
    {
        $field = FieldGeneric::make("body", [
            "type"                => "richtext",
            "pos"                 => 0,
            "toolbar"             => ["bold", "italic", "link"],
            "restrict_components" => true,
            "component_whitelist" => ["quote", "cta"],
        ]);

        $this->assertInstanceOf(FieldRichtext::class, $field);
        $this->assertSame(["bold", "italic", "link"], $field->toolbar());
        $this->assertTrue($field->restrictComponents());
        $this->assertSame(["quote", "cta"], $field->componentWhitelist());
    }

    public function testFieldRichtextDefaults(): void
    {
        $field = FieldGeneric::make("body", ["type" => "richtext", "pos" => 0]);
        $this->assertInstanceOf(FieldRichtext::class, $field);

        $this->assertSame([], $field->toolbar());
        $this->assertFalse($field->restrictComponents());
        $this->assertSame([], $field->componentWhitelist());
    }

    public function testFieldBloks(): void
    {
        $field = FieldGeneric::make("blocks", [
            "type"                => "bloks",
            "pos"                 => 0,
            "minimum"             => 1,
            "maximum"             => 5,
            "component_whitelist" => ["hero", "teaser"],
        ]);

        $this->assertInstanceOf(FieldBloks::class, $field);
        $this->assertSame(1, $field->minimum());
        $this->assertSame(5, $field->maximum());
        $this->assertSame(["hero", "teaser"], $field->componentWhitelist());
    }

    public function testFieldBloksNullableBounds(): void
    {
        $field = FieldGeneric::make("blocks", ["type" => "bloks", "pos" => 0]);
        $this->assertInstanceOf(FieldBloks::class, $field);

        $this->assertNull($field->minimum());
        $this->assertNull($field->maximum());
        $this->assertSame([], $field->componentWhitelist());
    }

    public function testFieldAsset(): void
    {
        $field = FieldGeneric::make("image", [
            "type"      => "asset",
            "pos"       => 0,
            "filetypes" => ["images"],
        ]);

        $this->assertInstanceOf(FieldAsset::class, $field);
        $this->assertSame(["images"], $field->filetypes());
    }

    public function testFieldAssetDefaultFiletypes(): void
    {
        $field = FieldGeneric::make("image", ["type" => "asset", "pos" => 0]);
        $this->assertInstanceOf(FieldAsset::class, $field);

        $this->assertSame([], $field->filetypes());
    }

    public function testFieldMultiasset(): void
    {
        $field = FieldGeneric::make("gallery", [
            "type"      => "multiasset",
            "pos"       => 0,
            "filetypes" => ["images", "videos"],
        ]);

        $this->assertInstanceOf(FieldMultiasset::class, $field);
        $this->assertSame(["images", "videos"], $field->filetypes());
    }

    public function testFieldMultilink(): void
    {
        $field = FieldGeneric::make("link", [
            "type"               => "multilink",
            "pos"                => 0,
            "link_types"         => ["url", "story"],
            "allow_target_blank" => true,
        ]);

        $this->assertInstanceOf(FieldMultilink::class, $field);
        $this->assertSame(["url", "story"], $field->linkTypes());
        $this->assertTrue($field->allowTargetBlank());
    }

    public function testFieldPlugin(): void
    {
        $field = FieldGeneric::make("custom", [
            "type"   => "plugin",
            "pos"    => 0,
            "plugin" => "example-plugin",
        ]);

        $this->assertInstanceOf(FieldPlugin::class, $field);
        $this->assertSame("example-plugin", $field->plugin());
    }

    public function testFieldPluginWithCurrentSchemaShape(): void
    {
        $field = FieldGeneric::make("custom", [
            "type"       => "custom",
            "pos"        => 0,
            "field_type" => "example-plugin",
        ]);

        $this->assertInstanceOf(FieldPlugin::class, $field);
        $this->assertSame("custom", $field->type());
        $this->assertSame("example-plugin", $field->plugin());
        $this->assertSame("example-plugin", $field->fieldType());
    }

    public function testFieldPluginUsesCurrentSchemaShapeWhenBuiltFluently(): void
    {
        $field = (new FieldPlugin("custom"))
            ->setPos(0)
            ->setPlugin("example-plugin");

        $this->assertSame("custom", $field->type());
        $this->assertSame("example-plugin", $field->plugin());
        $this->assertSame("example-plugin", $field->toArray()["field_type"]);
        $this->assertArrayNotHasKey("plugin", $field->toArray());
    }

    public function testFluentBuilderSetsTypeAutomatically(): void
    {
        $this->assertSame("text", (new FieldText("f"))->type());
        $this->assertSame("textarea", (new FieldTextarea("f"))->type());
        $this->assertSame("markdown", (new FieldMarkdown("f"))->type());
        $this->assertSame("datetime", (new FieldDatetime("f"))->type());
        $this->assertSame("option", (new FieldOption("f"))->type());
        $this->assertSame("options", (new FieldOptions("f"))->type());
        $this->assertSame("number", (new FieldNumber("f"))->type());
        $this->assertSame("boolean", (new FieldBoolean("f"))->type());
        $this->assertSame("richtext", (new FieldRichtext("f"))->type());
        $this->assertSame("bloks", (new FieldBloks("f"))->type());
        $this->assertSame("asset", (new FieldAsset("f"))->type());
        $this->assertSame("multiasset", (new FieldMultiasset("f"))->type());
        $this->assertSame("multilink", (new FieldMultilink("f"))->type());
        $this->assertSame("table", (new FieldTable("f"))->type());
        $this->assertSame("custom", (new FieldPlugin("f"))->type());
        $this->assertSame("section", (new FieldSection("f"))->type());
    }

    public function testFluentBuilderSharedSetters(): void
    {
        $field = (new FieldText("headline"))
            ->setPos(0)
            ->setDisplayName("Headline")
            ->setRequired()
            ->setTranslatable()
            ->setDescription("Main headline")
            ->setTooltip();

        $this->assertSame("headline", $field->key());
        $this->assertSame("text", $field->type());
        $this->assertSame(0, $field->pos());
        $this->assertSame("Headline", $field->displayName());
        $this->assertTrue($field->required());
        $this->assertTrue($field->translatable());
        $this->assertSame("Main headline", $field->description());
        $this->assertTrue($field->tooltip());
    }

    public function testFluentBuilderFieldText(): void
    {
        $field = (new FieldText("title"))
            ->setPos(0)
            ->setDefaultValue("Untitled")
            ->setRegex("^[A-Z]");

        $this->assertSame("Untitled", $field->defaultValue());
        $this->assertSame("^[A-Z]", $field->regex());
        $this->assertSame("text", $field->toArray()["type"]);
    }

    public function testFluentBuilderFieldTextarea(): void
    {
        $field = (new FieldTextarea("summary"))
            ->setPos(0)
            ->setDefaultValue("Untitled")
            ->setRegex("^[A-Z]");

        $this->assertSame("Untitled", $field->defaultValue());
        $this->assertSame("^[A-Z]", $field->regex());
        $this->assertSame("textarea", $field->toArray()["type"]);
    }

    public function testFluentBuilderFieldMarkdown(): void
    {
        $field = (new FieldMarkdown("body"))
            ->setPos(0)
            ->setDefaultValue("# Untitled");

        $this->assertSame("# Untitled", $field->defaultValue());
        $this->assertSame("markdown", $field->toArray()["type"]);
    }

    public function testFluentBuilderFieldDatetime(): void
    {
        $field = (new FieldDatetime("starts_at"))
            ->setPos(0)
            ->setDefaultValue("2026-05-14 10:00");

        $this->assertSame("2026-05-14 10:00", $field->defaultValue());
        $this->assertSame("datetime", $field->toArray()["type"]);
    }

    public function testFluentBuilderFieldOption(): void
    {
        $options = [["name" => "News", "value" => "news"]];
        $field = (new FieldOption("category"))
            ->setPos(0)
            ->setOptions($options)
            ->setSource("internal")
            ->setDatasourceSlug("categories")
            ->setDefaultValue("news");

        $this->assertSame($options, $field->options());
        $this->assertSame("internal", $field->source());
        $this->assertSame("categories", $field->datasourceSlug());
        $this->assertSame("news", $field->defaultValue());
    }

    public function testFluentBuilderFieldOptions(): void
    {
        $options = [["name" => "News", "value" => "news"]];
        $field = (new FieldOptions("categories"))
            ->setPos(0)
            ->setOptions($options)
            ->setSource("internal")
            ->setDatasourceSlug("categories");

        $this->assertSame($options, $field->options());
        $this->assertSame("internal", $field->source());
        $this->assertSame("categories", $field->datasourceSlug());
    }

    public function testFluentBuilderFieldNumber(): void
    {
        $field = (new FieldNumber("score"))
            ->setPos(1)
            ->setDefaultValue("5")
            ->setMinValue(1)
            ->setMaxValue(100);

        $this->assertSame("5", $field->defaultValue());
        $this->assertSame(1, $field->minValue());
        $this->assertSame(100, $field->maxValue());
    }

    public function testFluentBuilderFieldBoolean(): void
    {
        $field = (new FieldBoolean("active"))
            ->setPos(2)
            ->setDefaultValue(true)
            ->setInlineLabel()
            ->setCheckboxLabel("Is active");

        $this->assertTrue($field->defaultValue());
        $this->assertTrue($field->inlineLabel());
        $this->assertSame("Is active", $field->checkboxLabel());
    }

    public function testFluentBuilderFieldRichtext(): void
    {
        $field = (new FieldRichtext("body"))
            ->setPos(3)
            ->setToolbar(["bold", "italic"])
            ->setRestrictComponents()
            ->setComponentWhitelist(["quote", "cta"]);

        $this->assertSame(["bold", "italic"], $field->toolbar());
        $this->assertTrue($field->restrictComponents());
        $this->assertSame(["quote", "cta"], $field->componentWhitelist());
    }

    public function testFluentBuilderFieldBloks(): void
    {
        $field = (new FieldBloks("blocks"))
            ->setPos(4)
            ->setMinimum(1)
            ->setMaximum(5)
            ->setComponentWhitelist(["hero", "teaser"]);

        $this->assertSame(1, $field->minimum());
        $this->assertSame(5, $field->maximum());
        $this->assertSame(["hero", "teaser"], $field->componentWhitelist());
    }

    public function testFluentBuilderFieldAsset(): void
    {
        $field = (new FieldAsset("image"))
            ->setPos(5)
            ->setFiletypes(["images"]);

        $this->assertSame(["images"], $field->filetypes());
    }

    public function testFluentBuilderFieldMultiasset(): void
    {
        $field = (new FieldMultiasset("gallery"))
            ->setPos(6)
            ->setFiletypes(["images", "videos"]);

        $this->assertSame(["images", "videos"], $field->filetypes());
    }

    public function testFluentBuilderFieldMultilink(): void
    {
        $field = (new FieldMultilink("link"))
            ->setPos(7)
            ->setLinkTypes(["url", "story"])
            ->setAllowTargetBlank();

        $this->assertSame(["url", "story"], $field->linkTypes());
        $this->assertTrue($field->allowTargetBlank());
    }

    public function testFluentBuilderFieldPlugin(): void
    {
        $field = (new FieldPlugin("custom"))
            ->setPos(8)
            ->setPlugin("example-plugin");

        $this->assertSame("example-plugin", $field->plugin());
    }

    public function testToArrayContainsAllSetAttributes(): void
    {
        $field = (new FieldText("headline"))
            ->setPos(0)
            ->setRequired()
            ->setDefaultValue("Hello");

        $data = $field->toArray();
        $this->assertSame("text", $data["type"]);
        $this->assertSame(0, $data["pos"]);
        $this->assertTrue($data["required"]);
        $this->assertSame("Hello", $data["default_value"]);
    }

    public function testGetReturnsRawAttribute(): void
    {
        $field = FieldGeneric::make("title", [
            "type"       => "text",
            "pos"        => 0,
            "max_length" => 120,
            "regex"      => "^[A-Z]",
        ]);

        $this->assertSame(120, $field->get("max_length"));
        $this->assertSame("^[A-Z]", $field->get("regex"));
    }

    public function testGetReturnsDefaultWhenAttributeAbsent(): void
    {
        $field = FieldGeneric::make("title", ["type" => "text", "pos" => 0]);

        $this->assertNull($field->get("max_length"));
        $this->assertSame(0, $field->get("max_length", 0));
    }

    public function testGetSupportsNestedKeys(): void
    {
        $field = FieldGeneric::make("category", [
            "type"    => "option",
            "pos"     => 0,
            "options" => [
                ["name" => "News", "value" => "news"],
                ["name" => "Tech", "value" => "tech"],
            ],
        ]);

        $this->assertSame("news", $field->get("options.0.value"));
        $this->assertSame("Tech", $field->get("options.1.name"));
    }
}
