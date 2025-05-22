<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\QueryParameters;

use Storyblok\ManagementApi\QueryParameters\Type\SortBy;

class ComponentsParams
{
    /**
     * @param array<string>|string|null $byIds
     */
    public function __construct(
        private readonly array|string|null $byIds = null,
        private readonly ?SortBy $sortBy = null,
        private readonly bool|null $isRoot = null,
        private readonly string|null $search = null,
        private readonly string|null $inGroup = null,
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

        if ($this->sortBy instanceof SortBy) {
            $array['sort_by'] = $this->sortBy->toString();
        }

        if ($this->isRoot === true) {
            $array['is_root'] = "1";
        }

        if (null !== $this->byIds) {
            if (is_array($this->byIds)) {
                $array['by_ids'] = implode(",", $this->byIds);
            }

            if (is_string($this->byIds)) {
                $array['by_ids'] = $this->byIds;
            }
        }

        if (null !== $this->search) {
            $array['search'] = $this->search;
        }

        if (null !== $this->inGroup) {
            $array['in_group'] = $this->inGroup;
        }

        return $array;
    }
}
