<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields\Schema;

interface FieldInterface
{
    public function key(): string;

    public function type(): string;

    public function pos(): int;

    public function displayName(): string;

    public function required(): bool;

    public function translatable(): bool;

    public function noTranslate(): bool;

    public function description(): string;

    public function tooltip(): bool;

    /**
     * Returns the field attributes as an array suitable for use in a component schema payload.
     * @return array<mixed>
     */
    public function toArray(): array;

    /**
     * Returns the raw value of any field attribute by name.
     * Use this to access attributes not covered by a typed method,
     * for example a custom property or a field-type-specific key
     * that the specialized class does not expose.
     *
     * Returns $defaultValue when the attribute is not present.
     *
     * Example:
     *   $field->get('regex')
     *   $field->get('max_length', 0)
     */
    public function get(int|string $key, mixed $defaultValue = null): mixed;
}
