<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

class Story extends StoryBaseData
{
    private string $defaultContentType = "";

    /**
     * @param string $name the space name
     */
    public function __construct(
        string $name,
        string $slug,
        StoryComponent $content,
    ) {
        $this->data = [];
        $this->data["name"] = $name;
        $this->data["slug"] = $slug;
        $this->data["content"] = $content->toArray();
    }

    /**
     * @param mixed[] $data
     * @throws StoryblokFormatException
     */
    public static function make(array $data = []): self
    {
        $dataObject = new StoryblokData($data);
        if (
            !(
                $dataObject->hasKey("name") &&
                $dataObject->hasKey("slug") &&
                $dataObject->hasKey("content") &&
                $dataObject->hasKey("content.component")
            )
        ) {
            // is not valid
        }

        $content = StoryComponent::make($dataObject->getArray("content"));

        $story = new Story(
            $dataObject->getString("name"),
            $dataObject->getString("slug"),
            $content,
        );
        $story->setData($dataObject->toArray());
        // validate
        if (!$story->isValid()) {
            throw new StoryblokFormatException("Story is not valid");
        }

        return $story;
    }

    public function setName(string $name): void
    {
        $this->set("name", $name);
    }

    public function setSlug(string $slug): void
    {
        $this->set("slug", $slug);
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->set("created_at", $createdAt);
    }

    public function setContent(StoryComponent $content): void
    {
        $this->set("content", $content->toArray());
    }

    public function content(): StoryComponent
    {
        $contentArray = $this->getArray("content");
        return StoryComponent::make($contentArray);
    }

    public function name(): string
    {
        return $this->getString("name");
    }

    /**
     * Get the folder id for the Story.
     *
     * @return int the identifier of the parent folder, 0 if the story is stored at the root level
     */
    public function folderId(): int
    {
        return (int) $this->getInt("parent_id", 0);
    }

    public function createdAt(string $format = "Y-m-d"): null|string
    {
        return $this->getFormattedDateTime("created_at", "", format: $format);
    }

    public function publishedAt(string $format = "Y-m-d"): null|string
    {
        return $this->getFormattedDateTime("published_at", "", format: $format);
    }

    public function updatedAt(): null|string
    {
        return $this->getFormattedDateTime("updated_at", "", format: "Y-m-d");
    }

    public function setContentType(string $componentName): self
    {
        $this->defaultContentType = $componentName;
        return $this;
    }

    public function defaultContentType(): string
    {
        return $this->defaultContentType;
    }

    public function id(): string
    {
        return $this->getString("id");
    }

    public function uuid(): string
    {
        return $this->getString("uuid");
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

    /**
     * Set tags for Story, from a `Tags` collection
     * @return $this
     */
    public function setTags(Tags $tags): self
    {
        return $this->setTagsFromArray($tags->getTagsArray());
    }

    /**
     * Set tags for Story, from a string of arrays like ["tag1", "tag2"]
     * @param string[] $tagsArray
     * @return $this
     */
    public function setTagsFromArray(array $tagsArray): self
    {
        $this->set("tag_list", $tagsArray);
        return $this;
    }

    /**
     * Set the folder for the Story.
     *
     * @param int|string $folderId identifier of the Folder where to store the story
     * @return $this
     */
    public function setFolderId(int|string $folderId): self
    {
        $this->set("parent_id", (int) $folderId);
        return $this;
    }
}
