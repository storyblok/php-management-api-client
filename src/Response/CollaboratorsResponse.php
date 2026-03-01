<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Response;

use Storyblok\ManagementApi\Data\Collaborators;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

class CollaboratorsResponse extends StoryblokResponse implements StoryblokResponseInterface
{
    #[\Override]
    public function data(): Collaborators
    {
        $key = "collaborators";
        $array = $this->toArray();
        if (array_key_exists($key, $array)) {
            return new Collaborators($array[$key]);
        }

        throw new StoryblokFormatException(sprintf("Expected '%s' in the response.", $key));
    }
}
