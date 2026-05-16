<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields\Schema;

final readonly class OptionValue
{
    private function __construct(
        private string $name,
        private string $value,
    ) {}

    public static function make(string $name, string $value): self
    {
        return new self($name, $value);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function value(): string
    {
        return $this->value;
    }

    /**
     * @return array{name: string, value: string}
     */
    public function toArray(): array
    {
        return [
            "name" => $this->name,
            "value" => $this->value,
        ];
    }
}
