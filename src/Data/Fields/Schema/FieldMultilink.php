<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields\Schema;

class FieldMultilink extends FieldGeneric
{
    public const TYPE = "multilink";

    /**
     * @param mixed[] $data
     */
    public function __construct(string $key, array $data = [])
    {
        $data["type"] = self::TYPE;
        parent::__construct($key, $data);
    }

    /**
     * @return array<mixed>
     */
    public function linkTypes(): array
    {
        return $this->getArray("link_types");
    }

    /**
     * @param string[] $linkTypes
     */
    public function setLinkTypes(array $linkTypes): static
    {
        $this->set("link_types", $linkTypes);
        return $this;
    }

    public function allowTargetBlank(): bool
    {
        return $this->getBoolean("allow_target_blank");
    }

    public function setAllowTargetBlank(bool $allow = true): static
    {
        $this->set("allow_target_blank", $allow);
        return $this;
    }
}
