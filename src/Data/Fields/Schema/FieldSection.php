<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields\Schema;

class FieldSection extends FieldGeneric
{
    public const TYPE = "section";

    /**
     * @param mixed[] $data
     */
    public function __construct(string $key, array $data = [])
    {
        $data["type"] = self::TYPE;
        parent::__construct($key, $data);
    }
}
