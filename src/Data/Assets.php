<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Data\StoryblokData;

class Assets extends StoryblokData
{
    #[\Override]
    public function getDataClass(): string
    {
        return Asset::class;
    }

    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    /**
     * @param array<string, array<mixed>> $data
     */
    public static function makeFromResponse(array $data): self
    {
        return new self($data["assets"] ?? []);
    }
}
