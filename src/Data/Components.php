<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Data\StoryblokData;

class Components extends StoryblokData
{
    #[\Override]
    public function getDataClass(): string
    {
        return Component::class;
    }

    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    public function howManyComponents(): int
    {
        return $this->count();
    }
}
