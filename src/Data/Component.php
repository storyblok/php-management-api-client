<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Data\Fields\Schema\FieldGeneric;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldInterface;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

class Component extends BaseData
{
    public const DEFAULT_DATE_FORMAT = "Y-m-d\TH:i:s.vT";

    /**
     * @param string $name the component name
     */
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
        if (!$dataObject->hasKey("name")) {
            throw new StoryblokFormatException(
                "Component is not valid, missing the name",
            );
        }

        $component = new Component($dataObject->getString("name"));
        $component->setData($dataObject->toArray());
        // validate
        if (!$component->isValid()) {
            if ($dataObject->getString("name") === "") {
                throw new StoryblokFormatException(
                    "Component is not valid, missing the name",
                );
            }

            throw new StoryblokFormatException(
                "Component <" .
                    $dataObject->getString("name") .
                    "> is not valid",
            );
        }

        return $component;
    }

    public function setName(string $name): self
    {
        $this->set("name", $name);
        return $this;
    }

    /**
     * Technical name used for component property in entries
     */
    public function name(): string
    {
        return $this->getString("name");
    }

    /**
     * Component name based on the technical name or display name
     */
    public function realName(): string
    {
        return $this->getString("real_name");
    }

    /**
     * Name that will be used in the editor interface
     */
    public function displayName(): string
    {
        return $this->getString("display_name");
    }

    /**
     * Name that will be used in the editor interface
     */
    public function setDisplayName(string $displayName): self
    {
        $this->set("display_name", $displayName);
        return $this;
    }

    /**
     * URL to the preview image, if uploaded
     */
    public function image(): string|null
    {
        return $this->getStringNullable("image");
    }

    /**
     * URL to the preview image, if uploaded
     */
    public function setImage(string $url): self
    {
        $this->set("image", $url);
        return $this;
    }

    /**
     * The field that is for preview in the interface (Preview Field)
     */
    public function previewField(): string
    {
        return $this->getString("preview_field");
    }

    /**
     * The field that is for preview in the interface (Preview Field)
     */
    public function setPreviewField(string $value): self
    {
        $this->set("preview_field", $value);
        return $this;
    }

    /**
     * Creation date
     */
    public function createdAt(
        string $format = self::DEFAULT_DATE_FORMAT,
    ): null|string {
        return $this->getFormattedDateTime("created_at", "", format: $format);
    }

    /**
     * Latest update date
     */
    public function updatedAt(
        string $format = self::DEFAULT_DATE_FORMAT,
    ): null|string {
        return $this->getFormattedDateTime("updated_at", "", format: $format);
    }

    /**
     * The numeric ID in string format "12345678"
     */
    public function id(): string
    {
        return $this->getString("id");
    }

    /**
     * True if the component can be used as a Content Type
     * for example if is a content-type or a universal
     */
    public function isRoot(): bool
    {
        return $this->getBoolean("is_root");
    }

    /**
     * If the component can be used as a Content Type
     */
    public function setRoot(bool $isRoot = true): self
    {
        $this->set("is_root", $isRoot);
        return $this;
    }

    /**
     * True if the component can be nested inside other components
     */
    public function isNestable(): bool
    {
        return $this->getBoolean("is_nestable");
    }

    /**
     * If the component can be nested inside other components
     */
    public function setNestable(bool $isNestable = true): self
    {
        $this->set("is_nestable", $isNestable);
        return $this;
    }

    /**
     * True if the component is a content type (is_root only)
     */
    public function isContentType(): bool
    {
        return $this->getBoolean("is_root") &&
            !$this->getBoolean("is_nestable");
    }

    /**
     * True if the component is universal (is_root and is_nestable)
     */
    public function isUniversal(): bool
    {
        return $this->getBoolean("is_root") && $this->getBoolean("is_nestable");
    }

    /**
     * Returns the component type as a string:
     * "universal", "content-type", "nestable", or "".
     */
    public function getComponentTypeDetail(): string
    {
        return match (true) {
            $this->isUniversal() => "universal",
            $this->isContentType() => "content-type",
            $this->isNestable() => "nestable",
            default => "",
        };
    }

    public function uuid(): string
    {
        return $this->getString("uuid");
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function getSchema(): array
    {
        /** @var array<string, array<mixed>> $schema */
        $schema = $this->getArray("schema");
        return $schema;
    }

    /**
     * @param mixed[] $schema
     */
    public function setSchema(array $schema): void
    {
        $this->set("schema", $schema);
    }

    /**
     * @param mixed[] $fieldAttributes
     */
    public function setField(string $name, array $fieldAttributes): void
    {
        $this->set("schema." . $name, $fieldAttributes);
    }

    /**
     * Adds a typed field to the component schema.
     * The field key and attributes are taken from the FieldInterface object.
     */
    public function addField(FieldInterface $field): self
    {
        $this->setField($field->key(), $field->toArray());
        return $this;
    }

    /**
     * Adds multiple typed fields to the component schema without changing pos.
     *
     * @param FieldInterface[] $fields
     */
    public function addFields(array $fields): self
    {
        foreach ($fields as $field) {
            $this->addField($field);
        }

        return $this;
    }

    /**
     * Appends a typed field at the end of the schema (pos = maxPos() + 1).
     * Does not shift any existing entry. Use insertField() to place a field
     * at a specific position in the middle of the schema.
     */
    public function appendField(FieldInterface $field): self
    {
        $pos = $this->maxPos() + 1;
        $this->setField($field->key(), array_merge($field->toArray(), ["pos" => $pos]));
        return $this;
    }

    /**
     * Appends multiple typed fields at the end of the schema in the given order.
     *
     * @param FieldInterface[] $fields
     */
    public function appendFields(array $fields): self
    {
        foreach ($fields as $field) {
            $this->appendField($field);
        }

        return $this;
    }

    /**
     * Inserts a typed field at a specific position.
     * Every existing schema entry (fields and tabs) at pos >= $atPos is shifted
     * up by one to make room, then the field is added with pos set to $atPos.
     * Use this instead of manually shifting entries before calling addField().
     */
    public function insertField(FieldInterface $field, int $atPos): self
    {
        $schema = $this->getSchema();
        foreach ($schema as $key => $entry) {
            if (!is_int($entry["pos"] ?? null)) {
                continue;
            }

            if ($entry["pos"] >= $atPos) {
                $schema[$key]["pos"] = $entry["pos"] + 1;
            }
        }

        $this->setSchema($schema);

        $this->setField($field->key(), array_merge($field->toArray(), ["pos" => $atPos]));
        return $this;
    }

    /**
     * Returns non-tab schema entries as FieldInterface objects, sorted by pos ascending.
     * When $tab is provided, returns only fields assigned to that tab display name.
     * @return array<string, FieldInterface>
     */
    public function getFields(?string $tab = null): array
    {
        /** @var array<string, array<mixed>> $raw */
        $raw = array_filter(
            $this->getSchema(),
            fn(array $entry): bool => ($entry["type"] ?? "") !== "tab",
        );
        uasort($raw, fn(array $a, array $b): int => ($a["pos"] ?? 0) <=> ($b["pos"] ?? 0));

        if ($tab !== null) {
            /** @var array<mixed> $tabKeys */
            $tabKeys = [];
            foreach ($this->getTabs() as $tabEntry) {
                if (($tabEntry["display_name"] ?? null) === $tab) {
                    /** @var array<mixed> $tabKeys */
                    $tabKeys = $tabEntry["keys"] ?? [];
                    break;
                }
            }

            /** @var array<string, array<mixed>> $raw */
            $raw = array_filter($raw, fn(array $_, string $key): bool => in_array($key, $tabKeys, true), ARRAY_FILTER_USE_BOTH);
        }

        $fields = [];
        foreach ($raw as $key => $data) {
            $fields[$key] = FieldGeneric::make($key, $data);
        }

        return $fields;
    }

    /**
     * Returns tab entries only, sorted by pos ascending.
     * @return array<string, mixed[]>
     */
    public function getTabs(): array
    {
        /** @var array<string, array<mixed>> $tabs */
        $tabs = array_filter(
            $this->getSchema(),
            fn(array $entry): bool => ($entry["type"] ?? "") === "tab",
        );
        uasort($tabs, fn(array $a, array $b): int => ($a["pos"] ?? 0) <=> ($b["pos"] ?? 0));
        return $tabs;
    }

    /**
     * Returns the display name of the tab a field belongs to, or null if the
     * field is not assigned to any tab.
     */
    public function getFieldTab(string $fieldName): string|null
    {
        foreach ($this->getTabs() as $tab) {
            /** @var array<mixed> $keys */
            $keys = $tab["keys"] ?? [];
            if (in_array($fieldName, $keys, true)) {
                $displayName = $tab["display_name"] ?? null;
                return is_string($displayName) ? $displayName : null;
            }
        }

        return null;
    }

    /**
     * Returns the highest pos value across all schema entries (fields and tabs).
     * Returns -1 when the schema is empty.
     * Use maxPos() + 1 to get the next available position for a new entry.
     */
    public function maxPos(): int
    {
        $max = -1;
        foreach ($this->getSchema() as $entry) {
            if (!is_array($entry)) {
                continue;
            }

            $raw = $entry["pos"] ?? null;
            if (is_int($raw)) {
                $pos = $raw;
            } elseif (is_string($raw) && is_numeric($raw)) {
                $pos = (int) $raw;
            } else {
                continue;
            }

            if ($pos > $max) {
                $max = $pos;
            }
        }

        return $max;
    }

    /**
     * Validates if the component data contains all required fields and valid values
     */
    public function isValid(): bool
    {
        return $this->hasKey("name");
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
