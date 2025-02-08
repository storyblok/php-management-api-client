<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

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




    public function howManySpaces(): int
    {
        return $this->count();
    }

}
