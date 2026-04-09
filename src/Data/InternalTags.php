<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

class InternalTags extends StoryblokData
{
    #[\Override]
    public function getDataClass(): string
    {
        return InternalTag::class;
    }

    /**
     * @param mixed[] $data
     */
    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }
}
