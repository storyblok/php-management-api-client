<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Data\StoryblokData;

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
        return new self($data);
    }

    /**
     * @param array<string, array<mixed>> $data
     */
    public static function makeFromResponse(array $data = []): self
    {
        return new self($data["stories"] ?? []);
    }

    public function howManyStories(): int
    {
        return $this->count();
    }
}
