<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

/**
 * Iterable collection of localized path response data.
 *
 * Iterating over this collection yields LocalizedPathData instances. The raw
 * response array remains available through toArray().
 */
class LocalizedPathsData extends StoryblokData
{
    /**
     * @return class-string<LocalizedPathData>
     */
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
