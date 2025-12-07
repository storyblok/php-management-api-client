<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\QueryParameters;

class WorkflowStageChangesParams
{
    /**
     * @param string|int $withStory Filter by the ID of the story
     */
    public function __construct(private readonly string|int $withStory) {}

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return ['with_story' => $this->withStory];
    }
}
