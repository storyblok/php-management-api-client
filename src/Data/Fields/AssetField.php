<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields;

use Storyblok\ManagementApi\Data\Asset;
use Storyblok\ManagementApi\Data\BaseData;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

class AssetField extends BaseData
{
    public function __construct(string $filename = "")
    {
        $this->data = [
            "id" => null,
            "alt" => "",
            "name" => "",
            "focus" => "",
            "title" => "",
            "source" => "",
            "filename" => $filename,
            "copyright" => "",
            "fieldtype" => "asset",
            "meta_data" => (object) [],
        ];
    }

    public static function makeFromAsset(Asset $asset): self
    {
        $field = new self();
        $attributes = [
            "id",
            "alt",
            "name",
            "focus",
            "title",
            "source",
            "filename",
            "copyright",
            "fieldtype",
            "meta_data",
        ];
        foreach ($attributes as $attribute) {
            $field->set($attribute, $asset->get($attribute));
        }

        $field->set("name", $asset->getString("name"));
        $field->set("filename", $asset->filenameCDN());
        $field->set("is_external_url", false);
        $field->set("meta_data", (object) $asset->getArray("meta_data"));
        return $field;
    }
}
