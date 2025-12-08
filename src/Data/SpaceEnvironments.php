<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Data\StoryblokData;

class SpaceEnvironments extends StoryblokData
{
    #[\Override]
    public function getDataClass(): string
    {
        return SpaceEnvironment::class;
    }

    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    public function howManyEnvironments(): int
    {
        return $this->count();
    }
}
