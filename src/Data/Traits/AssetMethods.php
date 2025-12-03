<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Traits;

trait AssetMethods
{
    public function id(): string
    {
        return $this->getString("id");
    }

    public function filename(): string
    {
        return $this->getString("filename", "");
    }

    public function alt(): string
    {
        return $this->getString("alt", "");
    }

    public function name(): string
    {
        return $this->getString("name", "");
    }

    public function focus(): string
    {
        return $this->getString("focus", "");
    }

    public function title(): string
    {
        return $this->getString("title", "");
    }

    public function source(): string
    {
        return $this->getString("source", "");
    }

    public function fieldtype(): string
    {
        return $this->getString("copyright", "");
    }

    public function copyright(): string
    {
        return $this->getString("copyright", "");
    }
}
