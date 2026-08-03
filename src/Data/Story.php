<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;
use Storyblok\ManagementApi\StoryblokUtils;

class Story extends StoryBaseData
{
    private string $defaultContentType = "";

    /**
     * @param string $name the space name
     */
    public function __construct(
        string $name,
        string $slug,
        StoryComponent $content,
    ) {
        $this->data = [];
        $this->data["name"] = $name;
        $this->data["slug"] = $slug;
        $this->data["content"] = $content->toArray();
    }

    /**
     * @param mixed[] $data
     * @throws StoryblokFormatException
     */
    public static function make(array $data = []): self
    {
        $dataObject = new StoryblokData($data);
        if (
            !(
                $dataObject->hasKey("name") &&
                $dataObject->hasKey("slug") &&
                $dataObject->hasKey("content") &&
                $dataObject->hasKey("content.component")
            )
        ) {
            // is not valid
        }

        $content = StoryComponent::make($dataObject->getArray("content"));

        $story = new Story(
            $dataObject->getString("name"),
            $dataObject->getString("slug"),
            $content,
        );
        $story->setData($dataObject->toArray());
        // validate
        if (!$story->isValid()) {
            throw new StoryblokFormatException("Story is not valid");
        }

        return $story;
    }

    public function setName(string $name): self
    {
        $this->set("name", $name);
        return $this;
    }

    public function setSlug(string $slug): self
    {
        $this->set("slug", $slug);
        return $this;
    }

    public function setCreatedAt(string $createdAt): self
    {
        $this->set("created_at", $createdAt);
        return $this;
    }

    public function setContent(StoryComponent $content): self
    {
        $this->set("content", $content->toArray());
        return $this;
    }

    public function content(): StoryComponent
    {
        $contentArray = $this->getArray("content");
        return StoryComponent::make($contentArray);
    }

    /**
     * Set a field inside the story content payload.
     *
     * The field path is relative to `content`, so `setContentField("headline", $value)`
     * is equivalent to `set("content.headline", $value)`.
     *
     * When `$language` is provided, the value is stored using Storyblok's
     * field-level translation key, e.g. `headline__i18n__de`.
     *
     * @param string $field    Field name or nested content path relative to `content`.
     * @param mixed  $value    Field value to store.
     * @param string $language Optional language code for field-level translations.
     */
    public function setContentField(string $field, mixed $value, string $language = ""): self
    {
        $this->set("content." . $this->contentFieldKey($field, $language), $value);
        return $this;
    }

    /**
     * Get a field from the story content payload.
     *
     * The field path is relative to `content`, so `getContentField("headline")`
     * is equivalent to `get("content.headline")`.
     *
     * When `$language` is provided, the value is read using Storyblok's
     * field-level translation key, e.g. `headline__i18n__de`.
     *
     * @param string $field    Field name or nested content path relative to `content`.
     * @param mixed  $default  Value returned when the field is missing.
     * @param string $language Optional language code for field-level translations.
     */
    public function getContentField(
        string $field,
        mixed $default = null,
        string $language = "",
    ): mixed {
        return $this->get("content." . $this->contentFieldKey($field, $language), $default, raw: true);
    }

    private function contentFieldKey(string $field, string $language = ""): string
    {
        if ($language === "") {
            return $field;
        }

        return sprintf("%s__i18n__%s", $field, $language);
    }

    /**
     * Get the folder id for the Story.
     *
     * @return int the identifier of the parent folder, 0 if the story is stored at the root level
     */
    public function folderId(): int
    {
        return (int) $this->getInt("parent_id", 0);
    }

    public function setContentType(string $componentName): self
    {
        $this->defaultContentType = $componentName;
        return $this;
    }

    public function defaultContentType(): string
    {
        return $this->defaultContentType;
    }

    /**
     * Set tags for Story, from a `Tags` collection
     */
    public function setTags(Tags $tags): self
    {
        return $this->setTagsFromArray($tags->getTagsArray());
    }

    /**
     * Set tags for Story, from a string of arrays like ["tag1", "tag2"]
     * @param string[] $tagsArray
     */
    public function setTagsFromArray(array $tagsArray): self
    {
        $this->set("tag_list", $tagsArray);
        return $this;
    }

    /**
     * Set the folder for the Story.
     *
     * @param int|string $folderId identifier of the Folder where to store the story
     */
    public function setFolderId(int|string $folderId): self
    {
        $this->set("parent_id", (int) $folderId);
        return $this;
    }

    /**
     * Mark this story as a folder.
     */
    public function setIsFolder(bool $isFolder = true): self
    {
        $this->set("is_folder", $isFolder);
        return $this;
    }

    /**
     * Set the default content type for stories created inside this folder.
     *
     * @param string $componentName the technical name of the component
     */
    public function setDefaultRoot(string $componentName): self
    {
        $this->set("default_root", $componentName);
        return $this;
    }

    /**
     * Disable (or enable) the Visual Editor for this story/folder.
     */
    public function setDisableFeEditor(bool $disable = true): self
    {
        $this->set("disable_fe_editor", $disable);
        return $this;
    }

    /**
     * Set translated slug attributes for create/update/delete payloads.
     *
     * @param array<int, TranslatedSlug|array<string, mixed>> $translatedSlugs
     */
    public function setTranslatedSlugsAttributes(array $translatedSlugs): self
    {
        $this->set(
            "translated_slugs_attributes",
            array_map(
                static fn(TranslatedSlug|array $translatedSlug): array => $translatedSlug instanceof TranslatedSlug
                    ? $translatedSlug->toArray()
                    : $translatedSlug,
                $translatedSlugs,
            ),
        );

        return $this;
    }

    /**
     * Add translated slug attributes to the create/update/delete payload.
     */
    public function addTranslatedSlug(TranslatedSlug $translatedSlug): self
    {
        $translatedSlugs = $this->getArray("translated_slugs_attributes");
        $translatedSlugs[] = $translatedSlug->toArray();
        $this->set("translated_slugs_attributes", $translatedSlugs);
        return $this;
    }

    /**
     * Return translated slug objects from a story response.
     */
    public function translatedSlugs(): TranslatedSlugsData
    {
        return TranslatedSlugsData::make($this->getArray("translated_slugs"));
    }

    /**
     * Return localized path objects from a story response.
     *
     * `localized_paths` is response-side data. Use `translated_slugs_attributes`
     * to create, update, or delete translated slugs.
     */
    public function localizedPaths(): LocalizedPathsData
    {
        return LocalizedPathsData::make($this->getArray("localized_paths"));
    }

    /**
     * Build a Story pre-configured as a folder.
     *
     * Mirrors the Storyblok UI "Create folder" dialog:
     *  - Name: mandatory
     *  - Slug: auto-generated from the name if null
     *  - Parent folder: 0 (root) by default
     *  - Default content type (`default_root`): optional
     *  - Specific content types (`content.content_types`): optional whitelist
     *  - Lock sub-folders content-type change
     *    (`content.lock_subfolders_content_types`): only meaningful when
     *    `$contentTypes` is non-empty
     *  - Disable visual editor (`disable_fe_editor`): optional
     *
     * @param string      $name                        Folder name (mandatory)
     * @param string|null $slug                        Slug, auto-generated from name if null
     * @param int         $parentId                    Parent folder ID, 0 for root
     * @param string|null $defaultContentType          Default content type (`default_root`)
     * @param string[]    $contentTypes                Allowed content types whitelist
     * @param bool        $lockSubfoldersContentTypes  Force sub-folders to inherit the restriction
     * @param bool        $disableFeEditor             Disable the Visual Editor
     */
    public static function asFolder(
        string $name,
        ?string $slug = null,
        int $parentId = 0,
        ?string $defaultContentType = null,
        array $contentTypes = [],
        bool $lockSubfoldersContentTypes = false,
        bool $disableFeEditor = false,
    ): self {
        $resolvedSlug = $slug ?? StoryblokUtils::slugify($name);

        $content = new StoryComponent("");
        if ($contentTypes !== []) {
            $content->setContentTypes($contentTypes);
            $content->setLockSubfoldersContentTypes($lockSubfoldersContentTypes);
        }

        $folder = new self($name, $resolvedSlug, $content);
        $folder->setIsFolder(true);
        $folder->setFolderId($parentId);

        if ($defaultContentType !== null && $defaultContentType !== "") {
            $folder->setDefaultRoot($defaultContentType);
        }

        if ($disableFeEditor) {
            $folder->setDisableFeEditor(true);
        }

        return $folder;
    }
}
