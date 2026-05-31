<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

class ExperimentVariant extends StoryblokData
{
    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    public function setName(string $name): self
    {
        $this->set('name', $name);
        return $this;
    }

    public function setDisplayName(string $displayName): self
    {
        $this->set('display_name', $displayName);
        return $this;
    }

    public function setWeight(int $weight): self
    {
        $this->set('weight', $weight);
        return $this;
    }

    public function setControl(bool $isControl): self
    {
        $this->set('is_control', $isControl);
        return $this;
    }

    public function name(): string
    {
        return $this->getString('name', "");
    }

    public function displayName(): string
    {
        return $this->getString('display_name', "");
    }

    public function weight(): int|null
    {
        return $this->getInt('weight');
    }

    public function isControl(): bool
    {
        return $this->getBoolean('is_control', false);
    }
}
