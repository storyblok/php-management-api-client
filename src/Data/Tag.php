<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

class Tag extends BaseData
{
    public function __construct(
        string $name
    ) {
        $this->data = [];
        $this->data['name'] = $name;
    }

    /**
     * @param mixed[] $data
     * @throws StoryblokFormatException
     */
    public static function make(array $data = []): self
    {
        $dataObject = new StoryblokData($data);
        if (!($dataObject->hasKey('name'))) {
            // is not valid
        }

        $tag = new Tag(
            $dataObject->getString("name")
        );
        $tag->setData($dataObject->toArray());
        // validate
        if (! $tag->isValid()) {
            throw new StoryblokFormatException("Tag is not valid");
        }

        return $tag;

    }

    public function isValid(): bool
    {
        return $this->hasKey('name');
    }

    public function name(): string
    {
        return $this->getString('name');
    }

    public function id(): string
    {
        return $this->getString('id');
    }

    public function taggingsCount(): int|null
    {
        return $this->getInt('taggings_count', 0);
    }

    public function tagOnStories(): int|null
    {
        return $this->getInt('tag_on_stories', 0);
    }
}
