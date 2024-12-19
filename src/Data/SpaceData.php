<?php

namespace Roberto\Storyblok\Mapi\Data;

use Roberto\Storyblok\Mapi\Data\StoryblokData;

class SpaceData extends StoryblokData
{
    public function __construct()
    {
        parent::__construct([]);
    }

    public function setName($name): void
    {
        $this->set('name', $name);
    }

    public function setDomain($domain): void
    {
        $this->set('domain', $domain);
    }

}
