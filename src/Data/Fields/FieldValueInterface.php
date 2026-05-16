<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields;

interface FieldValueInterface
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array;
}
