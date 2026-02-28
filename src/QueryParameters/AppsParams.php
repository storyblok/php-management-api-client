<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\QueryParameters;

class AppsParams
{
    public function __construct(
        private readonly string|int $spaceId,
        private readonly int $page = 1,
        private readonly int $perPage = 25,
    ) {}

    /**
     * @return array<string, int|string>
     */
    public function toArray(): array
    {
        return [
            'space_id' => $this->spaceId,
            'page' => $this->page,
            'per_page' => $this->perPage,
        ];
    }
}
