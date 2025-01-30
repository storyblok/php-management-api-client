<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\QueryParameters;

class PaginationParams
{
    public function __construct(
        private int $page = 1,
        private readonly int $perPage = 25,
    ) {}


    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return ['page' => $this->page, 'per_page' => $this->perPage];
    }

    public function page(): int
    {
        return $this->page;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function incrementPage(): void
    {
        ++$this->page;
    }


}
