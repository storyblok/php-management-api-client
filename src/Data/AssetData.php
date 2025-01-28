<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\StoryblokUtils;

class AssetData extends StoryblokData
{
    /*
     * The Asset data response payload doesn't have the typical
     * "asset" attribute (like the story, the space etc)
     * This is the reason why the makeFromResponse is not implemented here
     */

    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    public function id(): string
    {
        return $this->getString('id', "");
    }


    public function filenameCDN(): string
    {
        return str_replace(
            "https://s3.amazonaws.com/a.storyblok.com",
            "https://a.storyblok.com",
            $this->filename(),
        );
    }

    public function filename(): string
    {
        return $this->getString('filename', "");
    }

    public function contentType(): string
    {
        return $this->getString('content_type');
    }

    public function contentLength(): int|null
    {
        return $this->getInt('content_length');
    }


    public function createdAt(): null|string
    {
        return $this->getFormattedDateTime('created_at', "", format: "Y-m-d");
    }

    public function updatedAt(): null|string
    {
        return $this->getFormattedDateTime('updated_at', "", format: "Y-m-d");
    }

}
