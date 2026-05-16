<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields\Schema;

trait FieldNamedConstructor
{
    /**
     * @param mixed[] $data
     */
    public static function make(string $key, array $data = []): self
    {
        return new self($key, $data);
    }
}
