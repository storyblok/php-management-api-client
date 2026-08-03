# TODO / Implementation notes

Working notes for pending work. Not part of the shipped package.

## 1. Notes

Reminder: `StoryApi::update(..., lang: 'de')` controls per-language *publishing*
only (Management API semantics); it does NOT set translated field values. Field
translations are handled by `Story::setContentField($field, $value, $language)`.
Story create/update/delete payloads for translatable slugs are handled by
`TranslatedSlug` and `translated_slugs_attributes`.
Translated slug and localized path response data is exposed through typed
collections via `Story::translatedSlugs()` and `Story::localizedPaths()`.

## 2. Housekeeping

- README TOC is hand-maintained — update it when adding/renaming `##` sections.

## 3. 1.10.0 Candidates

- Consider `findByLang(string $language)` helpers on translated slug and
  localized path collections, if consumers need direct language lookup instead
  of iterating manually.
