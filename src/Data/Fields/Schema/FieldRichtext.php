<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields\Schema;

class FieldRichtext extends FieldGeneric
{
    public const TYPE = "richtext";

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
    public function toolbar(): array
    {
        return $this->getArray("toolbar");
    }

    /**
     * @param string[] $toolbar
     */
    public function setToolbar(array $toolbar): static
    {
        $this->set("toolbar", $toolbar);
        return $this;
    }

    public function restrictComponents(): bool
    {
        return $this->getBoolean("restrict_components");
    }

    public function setRestrictComponents(bool $restrict = true): static
    {
        $this->set("restrict_components", $restrict);
        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function componentWhitelist(): array
    {
        return $this->getArray("component_whitelist");
    }

    /**
     * @param string[] $whitelist
     */
    public function setComponentWhitelist(array $whitelist): static
    {
        $this->set("component_whitelist", $whitelist);
        return $this;
    }
}
