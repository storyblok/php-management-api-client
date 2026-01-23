<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Data\StoryblokData;

class Stories extends StoryblokData
{
    #[\Override]
    public function getDataClass(): string
    {
        return StoryCollectionItem::class;
    }

    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    public function howManyStories(): int
    {
        return $this->count();
    }

    /**
     * Returns an array of the UUIDs of each Story in the collection.
     *
     * @return array<string> Array of UUID strings indexed from 0
     */
    public function getUuids(): array
    {
        $array = [];
        /** @var Story $story */
        foreach ($this as $story) {
            $array[] = $story->uuid();
        }

        return $array;
    }
}
