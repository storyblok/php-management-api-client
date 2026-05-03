<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields\Schema;

class FieldNumber extends FieldGeneric
{
    public function defaultValue(): string
    {
        return $this->getString("default_value");
    }

    public function minValue(): int|null
    {
        return $this->getInt("min_value");
    }

    public function maxValue(): int|null
    {
        return $this->getInt("max_value");
    }
}
