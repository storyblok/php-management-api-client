<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields\Schema;

class FieldBoolean extends FieldGeneric
{
    public function defaultValue(): bool
    {
        return $this->getBoolean("default_value");
    }

    public function inlineLabel(): bool
    {
        return $this->getBoolean("inline_label");
    }

    public function checkboxLabel(): string
    {
        return $this->getString("checkbox_label");
    }
}
