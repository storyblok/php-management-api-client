<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

class Tags extends StoryblokData
{
    #[\Override]
    public function getDataClass(): string
    {
        return Tag::class;
    }

    /**
     * @param mixed[] $data
     */
    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    /**
     * @return string[]
     */
    public function getTagsArray(): array
    {
        /** @var string[] $tagsArray */
        $tagsArray = [];

        foreach ($this->data as $tag) {
            if (is_array($tag) && array_key_exists("name", $tag)) {
                $tagsArray[] = $tag["name"];
            }

        }

        return $tagsArray;
    }
}
