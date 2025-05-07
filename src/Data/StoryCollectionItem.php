<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

class StoryCollectionItem extends BaseData
{
    public function __construct()
    {
        $this->data = [];

    }

    /**
     * @param mixed[] $data
     * @throws StoryblokFormatException
     */
    public static function make(array $data = []): self
    {
        $dataObject = new StoryblokData($data);
        if (!($dataObject->hasKey('name') && $dataObject->hasKey('slug'))) {
            // is not valid
        }

        $storyItem = new StoryCollectionItem();
        $storyItem->setData($dataObject->toArray());
        // validate
        if (! $storyItem->isValid()) {
            throw new StoryblokFormatException("Story is not valid");
        }

        return $storyItem;

    }

    public function slug(): string
    {
        return $this->getString('slug');
    }

    public function name(): string
    {
        return $this->getString('name');
    }

    public function createdAt(string $format = "Y-m-d"): null|string
    {
        return $this->getFormattedDateTime('created_at', "", format: $format);
    }

    public function publishedAt(string $format = "Y-m-d"): null|string
    {
        return $this->getFormattedDateTime('published_at', "", format: $format);
    }

    public function updatedAt(): null|string
    {
        return $this->getFormattedDateTime('updated_at', "", format: "Y-m-d");
    }

    public function id(): string
    {
        return $this->getString('id');
    }

    public function uuid(): string
    {
        return $this->getString('uuid');
    }

    /**
     * Validates if the story data contains all required fields and valid values
     */
    public function isValid(): bool
    {
        if (!$this->hasKey('name') || in_array($this->getString('name'), ['', '0'], true)) {
            return false;
        }

        return $this->hasKey('slug') && !in_array($this->getString('slug'), ['', '0'], true);
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
}
