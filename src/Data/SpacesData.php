<?php

declare(strict_types=1);

namespace Storyblok\Mapi\Data;

class SpacesData extends StoryblokData
{
    #[\Override]
    public function getDataClass(): string
    {
        return SpaceData::class;
    }

    /**
     * @param array<mixed> $data
     */
    public static function makeFromResponse(array $data = []): self
    {
        return new self($data["spaces"] ?? []);
    }


    public function howManyStories(): int
    {
        return $this->count();
    }

}
