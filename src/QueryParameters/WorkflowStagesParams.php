<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\QueryParameters;

use Storyblok\ManagementApi\QueryParameters\Type\SortBy;

class WorkflowStagesParams
{
    /**
     * @param string|string[]|null $byIds
     */
    public function __construct(
        private readonly string|int|null $inWorkflowId = null,
        private readonly string|null $search = null,
        private readonly string|array|null $byIds = null,
        private readonly string|null $excludeId = null,
    ) {}

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $array = [];
        if (null !== $this->inWorkflowId) {
            $array['in_workflow'] = $this->inWorkflowId;
        }

        if (null !== $this->search) {
            $array['search'] = $this->search;
        }

        if (null !== $this->excludeId) {
            $array['exclude_id'] = $this->excludeId;
        }



        if (null !== $this->byIds) {
            if (is_array($this->byIds)) {
                $array['by_ids'] = implode(",", $this->byIds);
            }

            if (is_string($this->byIds)) {
                $array['by_ids'] = $this->byIds;
            }
        }

        return $array;
    }
}
