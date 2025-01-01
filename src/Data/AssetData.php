<?php

declare(strict_types=1);

namespace Roberto\Storyblok\Mapi\Data;

use Roberto\Storyblok\Mapi\StoryblokUtils;

class AssetData extends StoryblokData
{
    /**
     * @param array<mixed> $data
     */
    public static function makeFromResponse(array $data = []): self
    {
        return new self($data["asset"] ?? []);
    }


    public function id(): int|string
    {
        return $this->get('id', "");
    }


    public function filenameCDN(): int|string
    {
        $filename = $this->get('filename', "");
        return str_replace(
            "https://s3.amazonaws.com/a.storyblok.com",
            "https://a.storyblok.com",
            $filename,
        );
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
