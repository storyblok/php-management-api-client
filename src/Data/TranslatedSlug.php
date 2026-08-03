<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

/**
 * Payload helper for Storyblok `translated_slugs_attributes`.
 */
class TranslatedSlug extends StoryblokData
{
    /**
     * Create translated slug attributes.
     *
     * The translated slug ID is omitted on create and returned later in the
     * story response under `translated_slugs`.
     */
    public static function create(
        string $lang,
        string $slug,
        ?string $name = null,
        ?bool $published = null,
    ): self {
        $translatedSlug = new self([
            "lang" => $lang,
            "slug" => $slug,
        ]);

        if ($name !== null) {
            $translatedSlug->set("name", $name);
        }

        if ($published !== null) {
            $translatedSlug->set("published", $published);
        }

        return $translatedSlug;
    }

    /**
     * Update existing translated slug attributes.
     *
     * Storyblok requires the translated slug ID when updating.
     */
    public static function update(
        int|string $id,
        ?string $lang = null,
        ?string $slug = null,
        ?string $name = null,
        ?bool $published = null,
    ): self {
        $translatedSlug = new self([
            "id" => $id,
        ]);

        if ($lang !== null) {
            $translatedSlug->set("lang", $lang);
        }

        if ($slug !== null) {
            $translatedSlug->set("slug", $slug);
        }

        if ($name !== null) {
            $translatedSlug->set("name", $name);
        }

        if ($published !== null) {
            $translatedSlug->set("published", $published);
        }

        return $translatedSlug;
    }

    /**
     * Delete existing translated slug attributes.
     *
     * Storyblok requires `_destroy: true` together with the translated slug ID.
     */
    public static function delete(int|string $id): self
    {
        return new self([
            "id"       => $id,
            "_destroy" => true,
        ]);
    }
}
