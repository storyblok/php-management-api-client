<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields\Schema;

class FieldBloks extends FieldGeneric
{
    public function minimum(): int|null
    {
        return $this->getInt("minimum");
    }

    public function maximum(): int|null
    {
        return $this->getInt("maximum");
    }

    /**
     * @return array<mixed>
     */
    public function componentWhitelist(): array
    {
        return $this->getArray("component_whitelist");
    }
}
