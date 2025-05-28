<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Data\StoryblokData;

class ComponentFolders extends StoryblokData
{
    #[\Override]
    public function getDataClass(): string
    {
        return ComponentFolder::class;
    }

    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    public function howManyComponentFolders(): int
    {
        return $this->count();
    }
}
