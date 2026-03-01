<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

class Collaborators extends StoryblokData
{
    #[\Override]
    public function getDataClass(): string
    {
        return Collaborator::class;
    }

    /**
     * @param mixed[] $data
     */
    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    public function howManyCollaborators(): int
    {
        return $this->count();
    }
}
