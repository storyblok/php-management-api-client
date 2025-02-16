<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

class Spaces extends StoryblokData
{
    #[\Override]
    public function getDataClass(): string
    {
        return Space::class;
    }

    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    public function howManySpaces(): int
    {
        return $this->count();
    }
}
