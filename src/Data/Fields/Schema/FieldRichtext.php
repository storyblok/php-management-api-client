<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields\Schema;

class FieldRichtext extends FieldGeneric
{
    use FieldNamedConstructor;

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
    public function styleOptions(): array
    {
        return $this->getArray("style_options");
    }

    /**
     * @param array<mixed> $styleOptions
     */
    public function setStyleOptions(array $styleOptions): static
    {
        $this->set("style_options", $styleOptions);
        return $this;
    }

    public function customizeToolbar(): bool
    {
        return $this->getBoolean("customize_toolbar");
    }

    public function setCustomizeToolbar(bool $customizeToolbar = true): static
    {
        $this->set("customize_toolbar", $customizeToolbar);
        return $this;
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

    public function restrictType(): string
    {
        return $this->getString("restrict_type");
    }

    public function setRestrictType(string $restrictType): static
    {
        $this->set("restrict_type", $restrictType);
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

    /**
     * @return array<mixed>
     */
    public function componentDenylist(): array
    {
        return $this->getArray("component_denylist");
    }

    /**
     * @param string[] $denylist
     */
    public function setComponentDenylist(array $denylist): static
    {
        $this->set("component_denylist", $denylist);
        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function componentTagWhitelist(): array
    {
        return $this->getArray("component_tag_whitelist");
    }

    /**
     * @param int[] $whitelist
     */
    public function setComponentTagWhitelist(array $whitelist): static
    {
        $this->set("component_tag_whitelist", $whitelist);
        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function componentTagDenylist(): array
    {
        return $this->getArray("component_tag_denylist");
    }

    /**
     * @param int[] $denylist
     */
    public function setComponentTagDenylist(array $denylist): static
    {
        $this->set("component_tag_denylist", $denylist);
        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function componentGroupWhitelist(): array
    {
        return $this->getArray("component_group_whitelist");
    }

    /**
     * @param string[] $whitelist
     */
    public function setComponentGroupWhitelist(array $whitelist): static
    {
        $this->set("component_group_whitelist", $whitelist);
        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function componentGroupDenylist(): array
    {
        return $this->getArray("component_group_denylist");
    }

    /**
     * @param string[] $denylist
     */
    public function setComponentGroupDenylist(array $denylist): static
    {
        $this->set("component_group_denylist", $denylist);
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

    public function allowCustomAttributes(): bool
    {
        return $this->getBoolean("allow_custom_attributes");
    }

    public function setAllowCustomAttributes(bool $allow = true): static
    {
        $this->set("allow_custom_attributes", $allow);
        return $this;
    }
}
