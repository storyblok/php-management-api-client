<?php

declare(strict_types=1);

namespace Storyblok\Mapi\Data;

use Storyblok\Mapi\Data\StoryblokData;
use Storyblok\Mapi\StoryblokUtils;

class SpaceData extends StoryblokData
{
    /**
     * @param array<mixed> $data
     */
    public static function makeFromResponse(array $data = []): self
    {
        return new self($data["space"] ?? []);
    }

    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    public function setName(string $name): void
    {
        $this->set('name', $name);
    }

    public function setDomain(string $domain): void
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



    public function planDescription(): null|string
    {
        return StoryblokUtils::getPlanDescription($this->get('plan_level', ""));
    }
}
