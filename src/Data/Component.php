<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

class Component extends BaseData
{
    public const DEFAULT_DATE_FORMAT = "Y-m-d\TH:i:s.vT";

    /**
     * @param string $name the component name
     */
    public function __construct(
        string $name,
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
            throw new StoryblokFormatException("Component is not valid, missing the name");
        }

        $component = new Component(
            $dataObject->getString("name")
        );
        $component->setData($dataObject->toArray());
        // validate
        if (! $component->isValid()) {
            if ($dataObject->getString("name") === "") {
                throw new StoryblokFormatException("Component is not valid, missing the name");
            }

            throw new StoryblokFormatException("Component <" . $dataObject->getString("name") . "> is not valid");
        }

        return $component;

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
     * Component name based on the technical name or display name
     */
    public function realName(): string
    {
        return $this->getString('real_name');
    }

    /**
     * Name that will be used in the editor interface
     */
    public function displayName(): string
    {
        return $this->getString('display_name');
    }

    /**
     * Name that will be used in the editor interface
     */
    public function setDisplayName(string $displayName): void
    {
        $this->set('display_name', $displayName);
    }

    /**
     * URL to the preview image, if uploaded
     */
    public function image(): string|null
    {
        return $this->getStringNullable('image');
    }

    /**
     * URL to the preview image, if uploaded
     */
    public function setImage(string $url): void
    {
        $this->set('image', $url);
    }

    /**
     * The field that is for preview in the interface (Preview Field)
     */
    public function previewField(): string
    {
        return $this->getString('preview_field');
    }

    /**
     * The field that is for preview in the interface (Preview Field)
     */
    public function setPreviewField(string $value): void
    {
        $this->set('preview_field', $value);
    }

    /**
     * Creation date
     */
    public function createdAt(string $format = self::DEFAULT_DATE_FORMAT): null|string
    {
        return $this->getFormattedDateTime('created_at', "", format: $format);
    }

    /**
     * Latest update date
     */
    public function updatedAt(string $format = self::DEFAULT_DATE_FORMAT): null|string
    {
        return $this->getFormattedDateTime('updated_at', "", format: $format);
    }

    /**
     * The numeric ID in string format "12345678"
     */
    public function id(): string
    {
        return $this->getString('id');
    }

    /**
     * True if the component can be used as a Content Type
     */
    public function isRoot(): bool
    {
        return $this->getBoolean('is_root');
    }

    /**
     * If the component can be used as a Content Type
     */
    public function setRoot(bool $isRoot = true): void
    {
        $this->set('is_root', $isRoot);
    }

    public function uuid(): string
    {
        return $this->getString('uuid');
    }

    /**
     * @return mixed[]
     */
    public function getSchema(): array
    {
        return $this->getArray('schema');
    }

    /**
     * @param mixed[] $schema
     */
    public function setSchema(array $schema): void
    {
        $this->set('schema', $schema);
    }

    /**
     * @param mixed[] $fieldAttributes
     */
    public function setField(string $name, array $fieldAttributes): void
    {
        $this->set('schema.' . $name, $fieldAttributes);
    }

    /**
     * Validates if the component data contains all required fields and valid values
     */
    public function isValid(): bool
    {
        return $this->hasKey('name');
    }

    /**
     * Set tags for Component, from a `Tags` collection
     * @return $this
     */
    public function setTags(Tags $tags): self
    {

        return $this->setTagsFromArray($tags->getTagsArray());

    }

    /**
     * Set tags for Component, from a string of arrays like ["tag1", "tag2"]
     * @param string[] $tagsArray
     * @return $this
     */
    public function setTagsFromArray(array $tagsArray): self
    {
        $this->set("internal_tag_ids", $tagsArray);
        return $this;
    }
}
