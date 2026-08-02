# TODO / Implementation notes

Working notes for pending work. Not part of the shipped package.

## 1. README roadmap items (still open, still relevant)
From the README "Next implementations" section — both verified unimplemented in `src/`:

- Field-level i18n setter, e.g. `set("heading", $value, "de")` writing the
  `heading__i18n__de` key on story content. Higher value. Consider a dedicated
  method name (e.g. `setLocalizedContentField()`) to avoid overloading `set()`.
- `LocalizedPath` class for translatable slugs (per-language paths) on create/update.

Reminder: `StoryApi::update(..., lang: 'de')` controls per-language *publishing*
only (Management API semantics); it does NOT set translated field values. The two
concerns are distinct — the i18n setter above is what fills that gap.

## 2. Housekeeping

- README TOC is hand-maintained — update it when adding/renaming `##` sections.
