<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

class InternalTag extends BaseData
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

        $internalTag = new InternalTag(
            $dataObject->getString("name"),
        );
        $internalTag->setData($dataObject->toArray());
        if (!$internalTag->isValid()) {
            throw new StoryblokFormatException("InternalTag is not valid");
        }

        return $internalTag;
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

    public function objectType(): string
    {
        return $this->getString("object_type");
    }
}
