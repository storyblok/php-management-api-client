<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\QueryParameters;

use Storyblok\ManagementApi\QueryParameters\Type\SortBy;

class StoriesParams
{
    /**
     * @param array<string>|string|null $excludingIds
     * @param array<string>|string|null $byIds
     * @param array<string>|string|null $byUuids
     * @param array<string>|string|null $withTag
     * @param array<string>|string|null $bySlugs
     * @param array<string>|string|null $excludingSlugs
     * @param array<string>|string|null $inWorkflowStages
     * @param array<string>|string|null $byUuidsOrdered
     */
    public function __construct(
        private readonly string|null $containComponent = null,
        private readonly string|null $textSearch = null,
        private readonly ?SortBy $sortBy = null,
        private readonly bool|null $pinned = null,
        private readonly array|string|null $excludingIds = null,
        private readonly array|string|null $byIds = null,
        private readonly array|string|null $byUuids = null,
        private readonly array|string|null $withTag = null,
        private readonly bool|null $folderOnly = null,
        private readonly bool|null $storyOnly = null,
        private readonly string|int|null $withParent = null,
        private readonly string|null $startsWith = null,
        private readonly bool|null $inTrash = null,
        private readonly string|null $search = null,
        private readonly string|int|null $inRelease = null,
        private readonly bool|null $isPublished = null,
        private readonly array|string|null $bySlugs = null,
        private readonly bool|null $mine = null,
        private readonly array|string|null $excludingSlugs = null,
        private readonly array|string|null $inWorkflowStages = null,
        private readonly array|string|null $byUuidsOrdered = null,
        private readonly string|null $withSlug = null,
        private readonly bool|null $withSummary = null,
        private readonly string|null $scheduledAtGreaterThan = null,
        private readonly string|null $scheduledAtLessThan = null,
        private readonly bool|null $favorite = null,
        private readonly string|null $referenceSearch = null,
    ) {}

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $array = [];
        if (null !== $this->containComponent) {
            $array['contain_component'] = $this->containComponent;
        }

        if (null !== $this->textSearch) {
            $array['text_search'] = $this->textSearch;
        }

        if ($this->sortBy instanceof SortBy) {
            $array['sort_by'] = $this->sortBy->toString();
        }

        if ($this->pinned === true) {
            $array['pinned'] = "1";
        }

        if (null !== $this->excludingIds) {
            if (is_array($this->excludingIds)) {
                $array['excluding_ids'] = implode(",", $this->excludingIds);
            }

            if (is_string($this->excludingIds)) {
                $array['excluding_ids'] = $this->excludingIds;
            }
        }

        if (null !== $this->byIds) {
            if (is_array($this->byIds)) {
                $array['by_ids'] = implode(",", $this->byIds);
            }

            if (is_string($this->byIds)) {
                $array['by_ids'] = $this->byIds;
            }
        }

        if (null !== $this->byUuids) {
            if (is_array($this->byUuids)) {
                $array['by_uuids'] = implode(",", $this->byUuids);
            }

            if (is_string($this->byUuids)) {
                $array['by_uuids'] = $this->byUuids;
            }
        }

        if (null !== $this->withTag) {
            if (is_array($this->withTag)) {
                $array['with_tag'] = implode(",", $this->withTag);
            }

            if (is_string($this->withTag)) {
                $array['with_tag'] = $this->withTag;
            }
        }

        if ($this->folderOnly === true) {
            $array['folder_only'] = "true";
        }

        if ($this->storyOnly === true) {
            $array['story_only'] = "1";
        }

        if (null !== $this->withParent) {
            $array['with_parent'] = $this->withParent;
        }

        if (null !== $this->startsWith) {
            $array['starts_with'] = $this->startsWith;
        }

        if ($this->inTrash === true) {
            $array['in_trash'] = "1";
        }

        if (null !== $this->search) {
            $array['search'] = $this->search;
        }

        if (null !== $this->inRelease) {
            $array['in_release'] = $this->inRelease;
        }

        if ($this->isPublished === true) {
            $array['is_published'] = "1";
        }

        if (null !== $this->bySlugs) {
            if (is_array($this->bySlugs)) {
                $array['by_slugs'] = implode(",", $this->bySlugs);
            }

            if (is_string($this->bySlugs)) {
                $array['by_slugs'] = $this->bySlugs;
            }
        }

        if ($this->mine === true) {
            $array['mine'] = "true";
        }

        if (null !== $this->excludingSlugs) {
            if (is_array($this->excludingSlugs)) {
                $array['excluding_slugs'] = implode(",", $this->excludingSlugs);
            }

            if (is_string($this->excludingSlugs)) {
                $array['excluding_slugs'] = $this->excludingSlugs;
            }
        }

        if (null !== $this->inWorkflowStages) {
            if (is_array($this->inWorkflowStages)) {
                $array['in_workflow_stages'] = implode(",", $this->inWorkflowStages);
            }

            if (is_string($this->inWorkflowStages)) {
                $array['in_workflow_stages'] = $this->inWorkflowStages;
            }
        }

        if (null !== $this->byUuidsOrdered) {
            if (is_array($this->byUuidsOrdered)) {
                $array['by_uuids_ordered'] = implode(",", $this->byUuidsOrdered);
            }

            if (is_string($this->byUuidsOrdered)) {
                $array['by_uuids_ordered'] = $this->byUuidsOrdered;
            }
        }

        if (null !== $this->withSlug) {
            $array['with_slug'] = $this->withSlug;
        }

        if ($this->withSummary === true) {
            $array['with_summary'] = "1";
        }

        if (null !== $this->scheduledAtGreaterThan) {
            $array['scheduled_at_gt'] = $this->scheduledAtGreaterThan;
        }

        if (null !== $this->scheduledAtLessThan) {
            $array['scheduled_at_lt'] = $this->scheduledAtLessThan;
        }

        if ($this->favorite === true) {
            $array['favorite'] = "1";
        }

        if (null !== $this->referenceSearch) {
            $array['reference_search'] = $this->referenceSearch;
        }

        return $array;
    }
}
