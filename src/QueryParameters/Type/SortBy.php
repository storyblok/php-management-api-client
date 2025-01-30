<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\QueryParameters\Type;

class SortBy
{
    public function __construct(
        public string $field,
        public Direction $direction = Direction::Asc,
    ) {}

    public function toString(): string
    {
        return \sprintf('%s:%s', $this->field, $this->direction->value);
    }
}
