<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

class StoryVersions extends StoryblokData
{
    #[\Override]
    public function getDataClass(): string
    {
        return StoryVersion::class;
    }

    /**
     * @param mixed[] $data
     */
    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    public function howManyVersions(): int
    {
        return $this->count();
    }
}
