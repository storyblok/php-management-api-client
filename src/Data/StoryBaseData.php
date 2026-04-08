<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

abstract class StoryBaseData extends BaseData
{
    public function slug(): string
    {
        return $this->getString("slug");
    }

    public function name(): string
    {
        return $this->getString("name");
    }

    public function hasWorkflowStage(): bool
    {
        $workflowStageId = $this->getInt("stage.workflow_stage_id", 0);
        return $workflowStageId > 0;
    }

    public function hasTags(): bool
    {
        $tags = $this->getArray("tag_list", []);
        return $tags !== [];
    }

    public function tagListAsString(): string
    {
        /** @var array<string> $tags */
        $tags = $this->getArray("tag_list", []);
        return implode(", ", $tags);
    }

    /**
     * Returns the list of tags as array.
     *
     * @return array<mixed>
     */
    public function tagListAsArray(): array
    {
        return $this->getArray("tag_list", []);
    }

    public function id(): string
    {
        return $this->getString("id");
    }

    public function uuid(): string
    {
        return $this->getString("uuid");
    }

    public function createdAt(string $format = "Y-m-d"): null|string
    {
        return $this->getFormattedDateTime("created_at", "", format: $format);
    }

    public function publishedAt(string $format = "Y-m-d"): null|string
    {
        return $this->getFormattedDateTime("published_at", "", format: $format);
    }

    public function updatedAt(string $format = "Y-m-d"): null|string
    {
        return $this->getFormattedDateTime("updated_at", "", format: $format);
    }

    /**
     * Validates if the story data contains all required fields and valid values
     */
    public function isValid(): bool
    {
        if (
            !$this->hasKey("name") ||
            in_array($this->getString("name"), ["", "0"], true)
        ) {
            return false;
        }

        return $this->hasKey("slug") &&
            !in_array($this->getString("slug"), ["", "0"], true);
    }

    public function fullSlug(): string
    {
        return $this->getString("full_slug");
    }

    public function isStartpage(): bool
    {
        return $this->getBoolean("is_startpage", false);
    }

    public function parentId(): int
    {
        return $this->getIntStrict("parent_id", 0);
    }

    public function groupId(): string
    {
        return $this->getString("group_id");
    }

    public function releaseId(): int
    {
        return $this->getIntStrict("release_id", 0);
    }

    public function firstPublishedAt(string $format = "Y-m-d"): null|string
    {
        return $this->getFormattedDateTime("first_published_at", "", format: $format);
    }

    public function hasUnpublishedChanges(): bool
    {
        return $this->getBoolean("unpublished_changes", false);
    }

    public function workflowStageId(): ?int
    {
        $id = $this->getInt("stage.workflow_stage_id", 0);

        return $id > 0 ? $id : null;
    }
}
