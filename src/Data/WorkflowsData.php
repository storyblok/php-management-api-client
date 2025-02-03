<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

class WorkflowsData extends StoryblokData
{
    #[\Override]
    public function getDataClass(): string
    {
        return WorkflowData::class;
    }

    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    /**
     * @param array<string, array<mixed>> $data
     */
    public static function makeFromResponse(array $data = []): self
    {
        return new self($data["workflows"] ?? []);
    }


    public function howManyWorkflows(): int
    {
        return $this->count();
    }

}
