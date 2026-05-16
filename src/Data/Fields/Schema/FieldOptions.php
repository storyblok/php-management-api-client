<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields\Schema;

class FieldOptions extends FieldGeneric
{
    use FieldNamedConstructor;

    public const TYPE = "options";

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
    public function options(): array
    {
        return $this->getArray("options");
    }

    /**
     * @param array<mixed> $options
     */
    public function setOptions(array $options): static
    {
        $this->set("options", $options);
        return $this;
    }

    public function source(): string
    {
        return $this->getString("source");
    }

    public function setSource(string $source): static
    {
        $this->set("source", $source);
        return $this;
    }

    public function datasourceSlug(): string
    {
        return $this->getString("datasource_slug");
    }

    public function setDatasourceSlug(string $datasourceSlug): static
    {
        $this->set("datasource_slug", $datasourceSlug);
        return $this;
    }
}
