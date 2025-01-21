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

    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    /**
     * @param array<mixed> $data
     */
    public static function makeFromResponse(array $data = []): self
    {
        return new self($data["spaces"] ?? []);
    }


    public function howManySpaces(): int
    {
        return $this->count();
    }

}
