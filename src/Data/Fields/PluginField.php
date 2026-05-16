<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields;

use Storyblok\ManagementApi\Data\BaseData;

class PluginField extends BaseData implements FieldValueInterface
{
    /**
     * @param array<mixed> $data
     */
    public function __construct(string $plugin = "", array $data = [])
    {
        $this->data = $data;
        if ($plugin !== "" || !array_key_exists("plugin", $this->data)) {
            $this->data["plugin"] = $plugin;
        }
    }

    public function plugin(): string
    {
        return $this->getString("plugin");
    }

    public function setPlugin(string $plugin): static
    {
        $this->set("plugin", $plugin);
        return $this;
    }

    public function uid(): ?string
    {
        return $this->getStringNullable("_uid");
    }

    public function setUid(string $uid): static
    {
        $this->set("_uid", $uid);
        return $this;
    }
}
