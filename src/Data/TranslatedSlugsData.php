<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

/**
 * Iterable collection of translated slug response data.
 *
 * Iterating over this collection yields TranslatedSlugData instances. The raw
 * response array remains available through toArray().
 */
class TranslatedSlugsData extends StoryblokData
{
    /**
     * @return class-string<TranslatedSlugData>
     */
    #[\Override]
    public function getDataClass(): string
    {
        return TranslatedSlugData::class;
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
