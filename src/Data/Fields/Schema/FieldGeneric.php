<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields\Schema;

use Storyblok\ManagementApi\Data\BaseData;

class FieldGeneric extends BaseData implements FieldInterface
{
    /**
     * @param mixed[] $data
     */
    public function __construct(protected string $fieldKey, array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @param mixed[] $data
     */
    public static function make(string $key, array $data): FieldInterface
    {
        return match ($data["type"] ?? "") {
            "text"       => new FieldText($key, $data),
            "textarea"   => new FieldTextarea($key, $data),
            "markdown"   => new FieldMarkdown($key, $data),
            "datetime"   => new FieldDatetime($key, $data),
            "option"     => new FieldOption($key, $data),
            "options"    => new FieldOptions($key, $data),
            "bloks"      => new FieldBloks($key, $data),
            "number"     => new FieldNumber($key, $data),
            "boolean"    => new FieldBoolean($key, $data),
            "asset"      => new FieldAsset($key, $data),
            "multiasset" => new FieldMultiasset($key, $data),
            "multilink"  => new FieldMultilink($key, $data),
            "richtext"   => new FieldRichtext($key, $data),
            "table"      => new FieldTable($key, $data),
            "custom"     => new FieldPlugin($key, $data),
            "plugin"     => new FieldPlugin($key, $data),
            "section"    => new FieldSection($key, $data),
            default      => new self($key, $data),
        };
    }

    #[\Override]
    public function key(): string
    {
        return $this->fieldKey;
    }

    public function type(): string
    {
        return $this->getString("type");
    }

    public function pos(): int
    {
        return $this->getIntStrict("pos");
    }

    public function displayName(): string
    {
        return $this->getString("display_name");
    }

    public function required(): bool
    {
        return $this->getBoolean("required");
    }

    public function translatable(): bool
    {
        return $this->getBoolean("translatable");
    }

    public function noTranslate(): bool
    {
        return $this->getBoolean("no_translate");
    }

    public function description(): string
    {
        return $this->getString("description");
    }

    public function tooltip(): bool
    {
        return $this->getBoolean("tooltip");
    }

    public function setPos(int $pos): static
    {
        $this->set("pos", $pos);
        return $this;
    }

    public function setDisplayName(string $displayName): static
    {
        $this->set("display_name", $displayName);
        return $this;
    }

    public function setRequired(bool $required = true): static
    {
        $this->set("required", $required);
        return $this;
    }

    public function setTranslatable(bool $translatable = true): static
    {
        $this->set("translatable", $translatable);
        return $this;
    }

    public function setNoTranslate(bool $noTranslate = true): static
    {
        $this->set("no_translate", $noTranslate);
        return $this;
    }

    public function setDescription(string $description): static
    {
        $this->set("description", $description);
        return $this;
    }

    public function setTooltip(bool $tooltip = true): static
    {
        $this->set("tooltip", $tooltip);
        return $this;
    }
}
