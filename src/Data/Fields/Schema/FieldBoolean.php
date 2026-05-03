<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields\Schema;

class FieldBoolean extends FieldGeneric
{
    public const TYPE = "boolean";

    /**
     * @param mixed[] $data
     */
    public function __construct(string $key, array $data = [])
    {
        $data["type"] = self::TYPE;
        parent::__construct($key, $data);
    }

    public function defaultValue(): bool
    {
        return $this->getBoolean("default_value");
    }

    public function setDefaultValue(bool $value): static
    {
        $this->set("default_value", $value);
        return $this;
    }

    public function inlineLabel(): bool
    {
        return $this->getBoolean("inline_label");
    }

    public function setInlineLabel(bool $inlineLabel = true): static
    {
        $this->set("inline_label", $inlineLabel);
        return $this;
    }

    public function checkboxLabel(): string
    {
        return $this->getString("checkbox_label");
    }

    public function setCheckboxLabel(string $label): static
    {
        $this->set("checkbox_label", $label);
        return $this;
    }
}
