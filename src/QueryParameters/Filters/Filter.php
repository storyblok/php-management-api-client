<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\QueryParameters\Filters;

class Filter
{
    /**
     * @param array<string>|string $value
     */
    public function __construct(
        public readonly string $field,
        public readonly string $operator,
        public readonly array|string $value,
    ) {}

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {

        return [$this->field => [
            $this->operator => is_array($this->value) ? implode(',', $this->value) : $this->value,
        ]];
    }
}
