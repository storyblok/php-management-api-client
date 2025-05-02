<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Data\StoryblokData;

class Stories extends StoryblokData
{
    #[\Override]
    public function getDataClass(): string
    {
        return StoryItem::class;
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
}
