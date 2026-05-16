<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields\Schema;

trait HasOptions
{
    /**
     * @return array<mixed>
     */
    public function options(): array
    {
        return $this->getArray("options");
    }

    /**
     * @param array<mixed> $options
     */
    public function setOptions(array $options): static
    {
        $this->set(
            "options",
            array_map(
                fn(mixed $option): mixed => $option instanceof OptionValue
                    ? $option->toArray()
                    : $option,
                $options,
            ),
        );
        return $this;
    }

    public function addOption(string $name, string $value): static
    {
        return $this->addOptionValue(OptionValue::make($name, $value));
    }

    public function addOptionValue(OptionValue $option): static
    {
        $options = $this->options();
        $options[] = $option->toArray();
        $this->set("options", $options);
        return $this;
    }
}
