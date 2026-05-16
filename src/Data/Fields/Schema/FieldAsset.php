<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields\Schema;

class FieldAsset extends FieldGeneric
{
    use FieldNamedConstructor;

    public const TYPE = "asset";

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
    public function filetypes(): array
    {
        return $this->getArray("filetypes");
    }

    /**
     * @param string[] $filetypes
     */
    public function setFiletypes(array $filetypes): static
    {
        $this->set("filetypes", $filetypes);
        return $this;
    }
}
