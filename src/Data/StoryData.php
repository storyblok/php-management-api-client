<?php

declare(strict_types=1);

namespace Storyblok\Mapi\Data;

use Storyblok\Mapi\Data\StoryblokData;

class StoryData extends StoryblokData
{
    /**
     * @param array<mixed> $data
     */
    public static function makeFromResponse(array $data = []): self
    {
        return new self($data["story"] ?? []);
    }

    public function setName(string $name): void
    {
        $this->set('name', $name);
    }

    public function name(): string
    {
        return $this->get('name', "");
    }

    public function createdAt(): null|string
    {
        return $this->getFormattedDateTime('created_at', "", format: "Y-m-d");
    }

    public function updatedAt(): null|string
    {
        return $this->getFormattedDateTime('updated_at', "", format: "Y-m-d");
    }



}
