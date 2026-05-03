<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields\Schema;

class FieldAsset extends FieldGeneric
{
    /**
     * @return array<mixed>
     */
    public function filetypes(): array
    {
        return $this->getArray("filetypes");
    }
}
