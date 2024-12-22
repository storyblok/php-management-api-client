<?php

declare(strict_types=1);

namespace Roberto\Storyblok\Mapi\Data;

use Roberto\Storyblok\Mapi\Data\StoryblokData;

class StoriesData extends StoryblokData
{
    #[\Override]
    public function getDataClass(): string
    {
        return StoryData::class;
    }

    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data["stories"] ?? []);
    }

    public function howManyStories(): int
    {
        return $this->count();
    }

}
