<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields\Schema;

class FieldText extends FieldGeneric
{
    public function defaultValue(): string
    {
        return $this->getString("default_value");
    }

    public function regex(): string
    {
        return $this->getString("regex");
    }
}
