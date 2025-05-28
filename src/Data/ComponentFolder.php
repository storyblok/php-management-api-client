<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

class ComponentFolder extends BaseData
{
    /**
     * @param string $name the component name
     */
    public function __construct(
        string $name,
        string $parentId
    ) {
        $this->data = [];
        $this->data['name'] = $name;
        $this->data['parent_id'] = $parentId;
    }

    /**
     * @param mixed[] $data
     * @throws StoryblokFormatException
     */
    public static function make(array $data = []): self
    {
        $dataObject = new StoryblokData($data);
        if (!($dataObject->hasKey('name'))) {
            throw new StoryblokFormatException("Component Folder is not valid, missing the name");
        }

        $componentFolder = new ComponentFolder(
            $dataObject->getString("name"),
            $dataObject->getString("parent_id")
        );
        $componentFolder->setData($dataObject->toArray());
        // validate
        if (! $componentFolder->isValid()) {
            if ($dataObject->getString("name") === "") {
                throw new StoryblokFormatException("Component Folder is not valid");
            }

            throw new StoryblokFormatException("Component Folder <" . $dataObject->getString("name") . "> is not valid");
        }

        return $componentFolder;

    }

    public function setName(string $name): void
    {
        $this->set('name', $name);
    }

    /**
     * Technical name used for component property in entries
     */
    public function name(): string
    {
        return $this->getString('name');
    }

    /**
     * The numeric ID in string format "12345678"
     */
    public function id(): string
    {
        return $this->getString('id');
    }

    /**
     * The numeric parent ID in string format "12345678"
     */
    public function parentId(): string
    {
        return $this->getString('parent_id');
    }

    /**
     * The parent UUID in string format "12345678"
     */
    public function parentUuid(): string
    {
        return $this->getString('parent_uuid');
    }

    public function uuid(): string
    {
        return $this->getString('uuid');
    }

    /**
     * Validates if the component data contains all required fields and valid values
     */
    public function isValid(): bool
    {
        return $this->hasKey('name');
    }
}
