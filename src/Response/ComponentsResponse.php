<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Response;

use Storyblok\ManagementApi\Data\ComponentFolders;
use Storyblok\ManagementApi\Data\Components;
use Storyblok\ManagementApi\Data\Space;
use Storyblok\ManagementApi\Data\Spaces;
use Storyblok\ManagementApi\Data\Stories;
use Storyblok\ManagementApi\Data\StoryblokData;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;
use Storyblok\ManagementApi\Response\StoryblokResponse;

class ComponentsResponse extends StoryblokResponse implements StoryblokResponseInterface
{
    #[\Override]
    public function data(): Components
    {
        $key = "components";
        $array = $this->toArray();
        if (array_key_exists($key, $array)) {
            return new Components($array[$key]);
        }

        throw new StoryblokFormatException(sprintf("Expected '%s' in the response.", $key));
    }

    public function dataFolders(): ComponentFolders
    {
        $key = "component_groups";
        $array = $this->toArray();
        if (array_key_exists($key, $array)) {
            return new ComponentFolders($array[$key]);
        }

        throw new StoryblokFormatException(sprintf("Expected '%s' in the response.", $key));
    }
}
