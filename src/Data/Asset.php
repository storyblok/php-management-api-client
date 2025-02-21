<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

class Asset extends BaseData
{
    public function __construct(
        string $filename,
    ) {
        $this->data = [];
        $this->data['filename'] = $filename;
        $this->data['fieldtype'] = "asset";
    }

    /**
     * The Asset data response payload doesn't have the typical
     * "asset" attribute (like the story, the space etc)
     * @param mixed[] $data
     * @throws StoryblokFormatException
     */
    public static function make(array $data = []): self
    {
        $dataObject = new StoryblokData($data);
        if (! $dataObject->hasKey('fieldtype')) {
            $dataObject->set("fieldtype", "asset");
        }

        if (!($dataObject->hasKey('filename') && $dataObject->hasKey('fieldtype'))) {
            // is not valid
        }

        $asset = new Asset(
            $dataObject->getString("filename")
        );
        $asset->setData($dataObject->toArray());
        // validate
        if (! $asset->isValid()) {
            throw new StoryblokFormatException("Asset is not valid");
        }

        return $asset;
    }

    public function isValid(): bool
    {
        return $this->hasKey('filename');
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

    public function setExternalUrl($url): self
    {

        $this->set("filename", $url);
        $this->set("is_external_url", true);
        return $this;
    }
    public static function emptyAsset(): Asset
    {
        return self::make([
            "id" => null,
            "alt"=> null,
            "name"=> "",
            "focus"=> null,
            "title"=> null,
            "source"=> null,
            "filename"=> "",
            "copyright"=> null,
            "fieldtype"=> "asset",
            "meta_data"=> [],
        ]);

    }
}
