<?php

declare(strict_types=1);

namespace Storyblok\Mapi\Data;

use Storyblok\Mapi\StoryblokUtils;

class AssetData extends StoryblokData
{
    /*
     * The Asset data response payload doesnt' have the typical
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


    public function filenameCDN(): int|string
    {
        $filename = $this->getString('filename', "");
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
