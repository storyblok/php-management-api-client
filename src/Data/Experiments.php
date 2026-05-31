<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

class Experiments extends StoryblokData
{
    #[\Override]
    public function getDataClass(): string
    {
        return Experiment::class;
    }

    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function makeFromResponse(array $data = []): self
    {
        $experiments = $data["experiments"] ?? [];
        return new self(is_array($experiments) ? $experiments : []);
    }

    public function howManyExperiments(): int
    {
        return $this->count();
    }
}
