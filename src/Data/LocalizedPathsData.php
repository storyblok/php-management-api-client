<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

/**
 * Iterable collection of localized path response data.
 */
class LocalizedPathsData extends StoryblokData
{
    #[\Override]
    public function getDataClass(): string
    {
        return LocalizedPathData::class;
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
