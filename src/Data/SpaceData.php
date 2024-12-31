<?php

declare(strict_types=1);

namespace Roberto\Storyblok\Mapi\Data;

use Roberto\Storyblok\Mapi\Data\StoryblokData;

class SpaceData extends StoryblokData
{
    public static function makeFromResponse(array $data = []): self
    {
        return new self($data["space"] ?? []);
    }

    public function setName($name): void
    {
        $this->set('name', $name);
    }

    public function setDomain($domain): void
    {
        $this->set('domain', $domain);
    }

    public function name(): null|string
    {
        return $this->get('name', "");
    }

    public function createdAt(): null|string
    {
        return $this->getFormattedDateTime('created_at', "", format: "Y-m-d");
    }
}
