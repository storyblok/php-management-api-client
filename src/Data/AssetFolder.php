<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

class AssetFolder extends BaseData
{
    public function __construct(string $name)
    {
        $this->data = [];
        $this->data["name"] = $name;
    }

    /**
     * @param mixed[] $data
     * @throws StoryblokFormatException
     */
    public static function make(array $data = []): self
    {
        $dataObject = new StoryblokData($data);
        if (!($dataObject->hasKey("name"))) {
            // is not valid
        }

        $assetFolder = new AssetFolder(
            $dataObject->getString("name"),
        );
        $assetFolder->setData($dataObject->toArray());
        if (!$assetFolder->isValid()) {
            throw new StoryblokFormatException("AssetFolder is not valid");
        }

        return $assetFolder;
    }

    public function isValid(): bool
    {
        return $this->hasKey("name");
    }

    public function id(): string
    {
        return $this->getString("id");
    }

    public function name(): string
    {
        return $this->getString("name");
    }

    public function parentId(): int|null
    {
        return $this->getInt("parent_id");
    }
}
