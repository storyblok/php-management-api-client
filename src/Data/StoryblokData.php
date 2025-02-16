<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use ArrayAccess;
use ArrayObject;
use Countable;
use Iterator;

/**
 * Class StoryblokData
 * This is a Generic data value class, in the case you can't use
 * the StoryData, Space data value class
 */
class StoryblokData extends BaseData
{
    /**
     * @param array<mixed> $data The initial data to store in the object.
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Factory method to create a new instance of StoryblokData.
     *
     * @param array<mixed> $data The data to initialize the object with.
     * @return StoryblokData A new instance of StoryblokData.
     */
    public static function make(array $data = []): StoryblokData
    {
        return new StoryblokData($data);
    }
}
