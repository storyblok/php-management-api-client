<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields\Schema;

class FieldPlugin extends FieldGeneric
{
    use FieldNamedConstructor;

    public const TYPE = "custom";

    /**
     * @param mixed[] $data
     */
    public function __construct(string $key, array $data = [])
    {
        $data["type"] = self::TYPE;
        parent::__construct($key, $data);
    }

    public function plugin(): string
    {
        $fieldType = $this->getString("field_type");
        if ($fieldType !== "") {
            return $fieldType;
        }

        return $this->getString("plugin");
    }

    public function setPlugin(string $plugin): static
    {
        $this->set("field_type", $plugin);
        return $this;
    }

    public function fieldType(): string
    {
        return $this->plugin();
    }

    public function setFieldType(string $fieldType): static
    {
        $this->set("field_type", $fieldType);
        return $this;
    }
}
