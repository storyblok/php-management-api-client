<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

/**
 * Read-side localized path data returned on story responses.
 */
class LocalizedPathData extends StoryblokData
{
    /**
     * @param mixed[] $data
     */
    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    public function path(): string
    {
        return $this->getString("path");
    }

    public function name(): string
    {
        return $this->getString("name");
    }

    public function lang(): string
    {
        return $this->getString("lang");
    }

    public function published(): bool
    {
        return $this->getBoolean("published");
    }
}
