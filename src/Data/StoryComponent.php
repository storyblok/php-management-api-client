<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Data\BaseData;
use Storyblok\ManagementApi\Data\Fields\AssetField;
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
    public function __construct(string $contentType)
    {
        $this->data = [];
        $this->data["component"] = $contentType;
    }

    /**
     * @param mixed[] $data
     * @throws StoryblokFormatException
     */
    public static function make(array $data = []): self
    {
        if (!array_key_exists("component", $data)) {
            throw new StoryblokFormatException(
                "Story `content` is not valid, `component` property is missing.",
            );
        }

        $storyContent = new StoryComponent($data["component"]);
        $storyContent->setData($data);
        // validate
        if (!$storyContent->isValid()) {
            throw new StoryblokFormatException("Story content is not valid");
        }

        return $storyContent;
    }

    public function setComponent(string $component): self
    {
        $this->set("component", $component);
        return $this;
    }

    public function component(): string
    {
        return $this->getString("component");
    }

    /**
     * Validates if the story content contains all required fields and valid values
     */
    public function isValid(): bool
    {
        return $this->hasKey("component");
    }

    public function setAsset(string $field, Asset $asset): self
    {
        $this->set($field, $asset->toArray());
        return $this;
    }

    /**
     * Set an asset field on the content object.
     *
     * Accepts a field name and an AssetField instance, converts the AssetField
     * into an array (via `toArray()`), and stores it using the underlying `set()` method.
     *
     * @param string     $field       The name of the field to set (e.g. "image").
     * @param AssetField $assetField  The AssetField instance to assign.
     *
     * @return $this     Returns the current object for method chaining.
     *
     * @example
     * $content->setAssetField(
     *     'image',
     *     AssetField::makeFromAsset($assets)
     * );
     *
     * // Or indirectly through your wrapper:
     * $content->set('image', AssetField::makeFromAsset($assets)->toArray());
     */
    public function setAssetField(string $field, AssetField $assetField): self
    {
        $this->set($field, $assetField->toArray());
        return $this;
    }

    public function addBlock(string $field, StoryComponent $component): self
    {
        $blocks = $this->getArray($field);
        $blocks[] = $component->toArray();
        $this->set($field, $blocks);
        return $this;
    }
}
