<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\QueryParameters;

class InternalTagsParams
{
    /**
     * @param string|null $byObjectType Filter by object type (e.g. "asset", "component")
     * @param string|null $search Filter by tag name
     */
    public function __construct(
        private readonly string|null $byObjectType = null,
        private readonly string|null $search = null,
    ) {}

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        $array = [];
        if (null !== $this->byObjectType) {
            $array["by_object_type"] = $this->byObjectType;
        }

        if (null !== $this->search) {
            $array["search"] = $this->search;
        }

        return $array;
    }
}
