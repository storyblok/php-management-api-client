<?php

declare(strict_types=1);

namespace Roberto\Storyblok\Mapi\Data;

class SpacesData extends StoryblokData
{
    #[\Override]
    public function getDataClass(): string
    {
        return SpaceData::class;
    }


    public static function makeFromResponse(array $data = []): self
    {
        return new self($data["spaces"] ?? []);
    }


    public function howManyStories(): int
    {
        return $this->count();
    }

}
