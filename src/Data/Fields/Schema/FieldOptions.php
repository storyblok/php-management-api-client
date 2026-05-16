<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields\Schema;

class FieldOptions extends FieldGeneric
{
    use FieldNamedConstructor;
    use HasOptions;

    public const TYPE = "options";

    /**
     * @param mixed[] $data
     */
    public function __construct(string $key, array $data = [])
    {
        $data["type"] = self::TYPE;
        parent::__construct($key, $data);
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
