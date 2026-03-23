<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\QueryParameters;

class StoryVersionsParams
{
    public function __construct(
        private readonly ?int $byReleaseId = null,
        private readonly ?bool $showContent = null,
    ) {}

    /**
     * @return array<string, int|string>
     */
    public function toArray(): array
    {
        $array = [];

        if (null !== $this->byReleaseId) {
            $array['by_release_id'] = $this->byReleaseId;
        }

        if (null !== $this->showContent) {
            $array['show_content'] = $this->showContent ? "true" : "false";
        }

        return $array;
    }
}
