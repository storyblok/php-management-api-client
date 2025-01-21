<?php

declare(strict_types=1);

namespace Storyblok\Mapi\Data;

interface StoryblokDataInterface
{
    public function get(mixed $key, mixed $defaultValue = null, string $charNestedKey = ".", bool $raw = false): mixed;
}
