<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Data\BaseData;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

/**
 * Class StoryComponent
 *
 * Represents the content section within a Story object retrieved from the
 * Storyblok API.
 *
 * The content section is an object containing field data associated with the
 * specific story type's content structure.
 * It also includes a `component` property, which holds the technical name of
 * the story type.
 *
 * Example structure of content property in a story:
 * ```json
 * {
 *   "component": "page",
 *   "title": "Homepage",
 *   "body": [
 *     { "component": "text", "text": "Welcome to our website" }
 *   ]
 * }
 * ```
 */
class StoryComponent extends BaseData
{
    public function __construct(
        string $contentType,
    ) {
        $this->data = [];
        $this->data['component'] = $contentType;
    }

    /**
     * @param mixed[] $data
     * @throws StoryblokFormatException
     */
    public static function make(array $data = []): self
    {
        if (! array_key_exists("component", $data)) {
            throw new StoryblokFormatException(
                "Story `content` is not valid, `component` property is missing."
            );
        }

        $storyContent = new StoryComponent(
            $data["component"],
        );
        $storyContent->setData($data);
        // validate
        if (! $storyContent->isValid()) {
            throw new StoryblokFormatException("Story content is not valid");
        }

        return $storyContent;

    }

    public function setComponent(string $component): self
    {
        $this->set('component', $component);
        return $this;
    }

    public function component(): string
    {
        return $this->getString('component');
    }

    /**
     * Validates if the story content contains all required fields and valid values
     */
    public function isValid(): bool
    {
        return $this->hasKey('component');
    }

    public function setAsset(string $field, Asset $asset): self
    {
        $this->set($field, $asset->toArray());
        return $this;
    }

    public function addBlock(string $field, StoryComponent $component): self
    {
        $blocks =$this->getArray($field);
        $blocks[] = $component->toArray();
        $this->set($field, $blocks);
        return $this;
    }
}
