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
     * Return the richtext style option definitions.
     *
     * @return array<mixed>
     */
    public function styleOptions(): array
    {
        return $this->getArray("style_options");
    }

    /**
     * Set the richtext style option definitions.
     *
     * @param array<mixed> $styleOptions
     */
    public function setStyleOptions(array $styleOptions): static
    {
        $this->set("style_options", $styleOptions);
        return $this;
    }

    /**
     * Whether the richtext toolbar is customized.
     */
    public function customizeToolbar(): bool
    {
        return $this->getBoolean("customize_toolbar");
    }

    /**
     * Enable or disable richtext toolbar customization.
     */
    public function setCustomizeToolbar(bool $customizeToolbar = true): static
    {
        $this->set("customize_toolbar", $customizeToolbar);
        return $this;
    }

    /**
     * Return the enabled richtext toolbar buttons.
     *
     * @return array<mixed>
     */
    public function toolbar(): array
    {
        return $this->getArray("toolbar");
    }

    /**
     * Set the enabled richtext toolbar buttons.
     *
     * @param string[] $toolbar
     */
    public function setToolbar(array $toolbar): static
    {
        $this->set("toolbar", $toolbar);
        return $this;
    }

    /**
     * Whether richtext component insertion is restricted.
     */
    public function restrictComponents(): bool
    {
        return $this->getBoolean("restrict_components");
    }

    /**
     * Enable or disable richtext component insertion restrictions.
     */
    public function setRestrictComponents(bool $restrict = true): static
    {
        $this->set("restrict_components", $restrict);
        return $this;
    }

    /**
     * Return the richtext component restriction mode.
     */
    public function restrictType(): string
    {
        return $this->getString("restrict_type");
    }

    /**
     * Set the richtext component restriction mode.
     */
    public function setRestrictType(string $restrictType): static
    {
        $this->set("restrict_type", $restrictType);
        return $this;
    }

    /**
     * Return allowed component names.
     *
     * @return array<mixed>
     */
    public function componentWhitelist(): array
    {
        return $this->getArray("component_whitelist");
    }

    /**
     * Set allowed component names.
     *
     * @param string[] $whitelist
     */
    public function setComponentWhitelist(array $whitelist): static
    {
        $this->set("component_whitelist", $whitelist);
        return $this;
    }

    /**
     * Return denied component names.
     *
     * @return array<mixed>
     */
    public function componentDenylist(): array
    {
        return $this->getArray("component_denylist");
    }

    /**
     * Set denied component names.
     *
     * @param string[] $denylist
     */
    public function setComponentDenylist(array $denylist): static
    {
        $this->set("component_denylist", $denylist);
        return $this;
    }

    /**
     * Return allowed component tag IDs.
     *
     * @return array<mixed>
     */
    public function componentTagWhitelist(): array
    {
        return $this->getArray("component_tag_whitelist");
    }

    /**
     * Set allowed component tag IDs.
     *
     * @param int[] $whitelist
     */
    public function setComponentTagWhitelist(array $whitelist): static
    {
        $this->set("component_tag_whitelist", $whitelist);
        return $this;
    }

    /**
     * Return denied component tag IDs.
     *
     * @return array<mixed>
     */
    public function componentTagDenylist(): array
    {
        return $this->getArray("component_tag_denylist");
    }

    /**
     * Set denied component tag IDs.
     *
     * @param int[] $denylist
     */
    public function setComponentTagDenylist(array $denylist): static
    {
        $this->set("component_tag_denylist", $denylist);
        return $this;
    }

    /**
     * Return allowed component group UUIDs.
     *
     * @return array<mixed>
     */
    public function componentGroupWhitelist(): array
    {
        return $this->getArray("component_group_whitelist");
    }

    /**
     * Set allowed component group UUIDs.
     *
     * @param string[] $whitelist
     */
    public function setComponentGroupWhitelist(array $whitelist): static
    {
        $this->set("component_group_whitelist", $whitelist);
        return $this;
    }

    /**
     * Return denied component group UUIDs.
     *
     * @return array<mixed>
     */
    public function componentGroupDenylist(): array
    {
        return $this->getArray("component_group_denylist");
    }

    /**
     * Set denied component group UUIDs.
     *
     * @param string[] $denylist
     */
    public function setComponentGroupDenylist(array $denylist): static
    {
        $this->set("component_group_denylist", $denylist);
        return $this;
    }

    /**
     * Whether richtext links allow opening in a new tab.
     */
    public function allowTargetBlank(): bool
    {
        return $this->getBoolean("allow_target_blank");
    }

    /**
     * Enable or disable opening richtext links in a new tab.
     */
    public function setAllowTargetBlank(bool $allow = true): static
    {
        $this->set("allow_target_blank", $allow);
        return $this;
    }

    /**
     * Whether richtext links allow custom attributes.
     */
    public function allowCustomAttributes(): bool
    {
        return $this->getBoolean("allow_custom_attributes");
    }

    /**
     * Enable or disable custom attributes on richtext links.
     */
    public function setAllowCustomAttributes(bool $allow = true): static
    {
        $this->set("allow_custom_attributes", $allow);
        return $this;
    }
}
