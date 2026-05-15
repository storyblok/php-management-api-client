<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields\Schema;

class FieldDatetime extends FieldGeneric
{
    public const TYPE = "datetime";

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
}
