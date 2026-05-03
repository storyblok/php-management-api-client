<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields\Schema;

class FieldRichtext extends FieldGeneric
{
    /**
     * @return array<mixed>
     */
    public function toolbar(): array
    {
        return $this->getArray("toolbar");
    }

    public function restrictComponents(): bool
    {
        return $this->getBoolean("restrict_components");
    }
}
