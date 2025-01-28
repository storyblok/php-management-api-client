<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

class TagData extends StoryblokData
{
    /**
     * @param array<string, array<mixed>> $data
     */
    public static function makeFromResponse(array $data = []): self
    {
        return new self($data["tag"] ?? []);
    }

    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    public function name(): string
    {
        return $this->getString('name');
    }

    public function taggingsCount(): int|null
    {
        return $this->getInt('taggings_count', 0);
    }

    public function tagOnStories(): int|null
    {
        return $this->getInt('tag_on_stories', 0);
    }


}
