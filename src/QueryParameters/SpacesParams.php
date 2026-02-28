<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\QueryParameters;

class SpacesParams
{
    public function __construct(
        private readonly ?string $search = null,
    ) {}

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $array = [];

        if (null !== $this->search) {
            $array['search'] = $this->search;
        }

        return $array;
    }
}
