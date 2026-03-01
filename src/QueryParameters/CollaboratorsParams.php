<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\QueryParameters;

class CollaboratorsParams
{
    /**
     * @param array<string>|string|null $bySpaceIds
     */
    public function __construct(
        private readonly array|string|null $bySpaceIds = null,
    ) {}

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        $array = [];

        if (null !== $this->bySpaceIds) {
            $array['by_space_ids'] = is_array($this->bySpaceIds)
                ? implode(",", $this->bySpaceIds)
                : $this->bySpaceIds;
        }

        return $array;
    }
}
