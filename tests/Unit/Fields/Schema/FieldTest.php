<?php

declare(strict_types=1);

namespace Tests\Unit\Fields\Schema;

use Storyblok\ManagementApi\Data\Fields\Schema\FieldAsset;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldBloks;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldBoolean;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldGeneric;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldMultiasset;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldNumber;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldRichtext;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldText;
use Tests\TestCase;

final class FieldTest extends TestCase
{
    public function testFactoryReturnsCorrectTypes(): void
    {
        $this->assertInstanceOf(FieldText::class, FieldGeneric::make("f", ["type" => "text"]));
        $this->assertInstanceOf(FieldBloks::class, FieldGeneric::make("f", ["type" => "bloks"]));
        $this->assertInstanceOf(FieldNumber::class, FieldGeneric::make("f", ["type" => "number"]));
        $this->assertInstanceOf(FieldBoolean::class, FieldGeneric::make("f", ["type" => "boolean"]));
        $this->assertInstanceOf(FieldAsset::class, FieldGeneric::make("f", ["type" => "asset"]));
        $this->assertInstanceOf(FieldMultiasset::class, FieldGeneric::make("f", ["type" => "multiasset"]));
        $this->assertInstanceOf(FieldRichtext::class, FieldGeneric::make("f", ["type" => "richtext"]));
        $this->assertInstanceOf(FieldGeneric::class, FieldGeneric::make("f", ["type" => "textarea"]));
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
        ]);

        $this->assertInstanceOf(FieldRichtext::class, $field);
        $this->assertSame(["bold", "italic", "link"], $field->toolbar());
        $this->assertTrue($field->restrictComponents());
    }

    public function testFieldRichtextDefaults(): void
    {
        $field = FieldGeneric::make("body", ["type" => "richtext", "pos" => 0]);
        $this->assertInstanceOf(FieldRichtext::class, $field);

        $this->assertSame([], $field->toolbar());
        $this->assertFalse($field->restrictComponents());
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

    public function testFluentBuilderSetsTypeAutomatically(): void
    {
        $this->assertSame("text", (new FieldText("f"))->type());
        $this->assertSame("number", (new FieldNumber("f"))->type());
        $this->assertSame("boolean", (new FieldBoolean("f"))->type());
        $this->assertSame("richtext", (new FieldRichtext("f"))->type());
        $this->assertSame("bloks", (new FieldBloks("f"))->type());
        $this->assertSame("asset", (new FieldAsset("f"))->type());
        $this->assertSame("multiasset", (new FieldMultiasset("f"))->type());
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
            ->setRestrictComponents();

        $this->assertSame(["bold", "italic"], $field->toolbar());
        $this->assertTrue($field->restrictComponents());
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
