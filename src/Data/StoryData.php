<?php

declare(strict_types=1);

namespace Storyblok\Mapi\Data;

use Storyblok\Mapi\Data\StoryblokData;

class StoryData extends StoryblokData
{
    public function setName(string $name): void
    {
        $this->set('name', $name);
    }

    public function getName(): string
    {
        return $this->get('name', "");
    }



}
