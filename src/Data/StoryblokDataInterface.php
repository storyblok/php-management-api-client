<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

interface StoryblokDataInterface
{
    public function get(mixed $key, mixed $defaultValue = null, string $charNestedKey = ".", bool $raw = false): mixed;

    public function getString(mixed $key, string $defaultValue = "", string $charNestedKey = "."): string;
}
