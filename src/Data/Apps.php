<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

class Apps extends StoryblokData
{
    #[\Override]
    public function getDataClass(): string
    {
        return App::class;
    }

    /**
     * @param mixed[] $data
     */
    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    public function howManyApps(): int
    {
        return $this->count();
    }
}
