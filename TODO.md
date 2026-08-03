# TODO / Implementation notes

Working notes for pending work. Not part of the shipped package.

## 1. README roadmap items (still open, still relevant)
From the README "Next implementations" section:

- `LocalizedPath` class for translatable slugs (per-language paths) on create/update.

Reminder: `StoryApi::update(..., lang: 'de')` controls per-language *publishing*
only (Management API semantics); it does NOT set translated field values. Field
translations are handled by `Story::setContentField($field, $value, $language)`.

## 2. Housekeeping

- README TOC is hand-maintained — update it when adding/renaming `##` sections.
