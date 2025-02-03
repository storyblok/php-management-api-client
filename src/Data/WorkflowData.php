<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Data\StoryblokData;
use Storyblok\ManagementApi\StoryblokUtils;

class WorkflowData extends StoryblokData
{
    /**
     * @param array<string, array<mixed>> $data
     */
    public static function makeFromResponse(array $data = []): self
    {
        return new self($data["workflow"] ?? []);
    }

    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    public function setName(string $name): void
    {
        $this->set('name', $name);
    }



    public function name(): string
    {
        return $this->getString('name', "");
    }

    public function id(): string
    {
        return $this->getString('id', "");
    }

    public function isDefault(): bool
    {
        return $this->getBoolean('is_default', false);
    }

    /**
     * @return mixed[]|null
     */
    public function contentTypes(): null|array
    {
        return $this->getArray('content_types', []);
    }

}
