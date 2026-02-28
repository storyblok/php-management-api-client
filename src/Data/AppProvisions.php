<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

class AppProvisions extends StoryblokData
{
    #[\Override]
    public function getDataClass(): string
    {
        return AppProvision::class;
    }

    /**
     * @param mixed[] $data
     */
    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    public function howManyAppProvisions(): int
    {
        return $this->count();
    }
}
