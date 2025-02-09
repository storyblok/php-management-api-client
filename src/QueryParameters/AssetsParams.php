<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\QueryParameters;

use Storyblok\ManagementApi\QueryParameters\Type\SortBy;

class AssetsParams
{
    /**
     * @param bool|null $isPrivate only displays private assets
     * @param string|null $search Provide a search term to filter a specific file by the filename
     * @param string|null $byAlt Filter by the alt text of an asset
     * @param string|null $byCopyright Filter by the copyright of an asset
     * @param string|null $byTitle Filter by the title of an asset
     * @param array<string>|string|null $withTags Filter by specific tags
     */
    public function __construct(
        private readonly int|string|null $inFolder = null,
        private readonly ?SortBy $sortBy = null,
        private readonly bool|null $isPrivate = null,
        private readonly string|null $search = null,
        private readonly string|null $byAlt = null,
        private readonly string|null $byCopyright = null,
        private readonly string|null $byTitle = null,
        private readonly array|string|null $withTags = null,
    ) {}

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $array = [];
        if (null !== $this->inFolder) {
            $array['in_folder'] = $this->inFolder;
        }

        if ($this->sortBy instanceof \Storyblok\ManagementApi\QueryParameters\Type\SortBy) {
            $array['sort_by'] = $this->sortBy->toString();
        }

        if (null !== $this->isPrivate && $this->isPrivate) {
            $array['is_private'] = "1";
        }

        if (null !== $this->search) {
            $array['search'] = $this->search;
        }

        if (null !== $this->byAlt) {
            $array['by_alt'] = $this->byAlt;
        }

        if (null !== $this->byCopyright) {
            $array['by_copyright'] = $this->byCopyright;
        }

        if (null !== $this->byTitle) {
            $array['by_title'] = $this->byTitle;
        }

        if (null !== $this->withTags) {
            if (is_array($this->withTags)) {
                $array['with_tags'] = implode(",", $this->withTags);
            }

            if (is_string($this->withTags)) {
                $array['with_tags'] = $this->withTags;
            }

        }

        return $array;
    }
}
