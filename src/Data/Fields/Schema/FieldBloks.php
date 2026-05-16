<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields\Schema;

class FieldBloks extends FieldGeneric
{
    use FieldNamedConstructor;

    public const TYPE = "bloks";

    /**
     * @param mixed[] $data
     */
    public function __construct(string $key, array $data = [])
    {
        $data["type"] = self::TYPE;
        parent::__construct($key, $data);
    }

    public function minimum(): int|null
    {
        return $this->getInt("minimum");
    }

    public function setMinimum(int $min): static
    {
        $this->set("minimum", $min);
        return $this;
    }

    public function maximum(): int|null
    {
        return $this->getInt("maximum");
    }

    public function setMaximum(int $max): static
    {
        $this->set("maximum", $max);
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
