<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

interface StoryblokDataInterface
{
    public function get(
        int|string $key,
        mixed $defaultValue = null,
        string $charNestedKey = ".",
        bool $raw = false,
    ): mixed;

    public function getString(
        int|string $key,
        string $defaultValue = "",
        string $charNestedKey = ".",
    ): string;
}
