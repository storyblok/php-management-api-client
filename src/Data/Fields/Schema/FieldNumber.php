<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields\Schema;

class FieldNumber extends FieldGeneric
{
    public const TYPE = "number";

    /**
     * @param mixed[] $data
     */
    public function __construct(string $key, array $data = [])
    {
        $data["type"] = self::TYPE;
        parent::__construct($key, $data);
    }

    public function defaultValue(): string
    {
        return $this->getString("default_value");
    }

    public function setDefaultValue(string $value): static
    {
        $this->set("default_value", $value);
        return $this;
    }

    public function minValue(): int|null
    {
        return $this->getInt("min_value");
    }

    public function setMinValue(int $min): static
    {
        $this->set("min_value", $min);
        return $this;
    }

    public function maxValue(): int|null
    {
        return $this->getInt("max_value");
    }

    public function setMaxValue(int $max): static
    {
        $this->set("max_value", $max);
        return $this;
    }
}
