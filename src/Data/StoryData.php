<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Data\StoryblokData;

class StoryData extends StoryblokData
{
    private string $defaultContentType = "";

    /**
     * @param array<string, array<mixed>> $data
     */
    public static function makeFromResponse(array $data = []): self
    {
        return new self($data["story"] ?? []);
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

    public function setSlug(string $slug): void
    {
        $this->set('slug', $slug);
    }

    public function slug(): string
    {
        return $this->getString('slug');
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->set('created_at', $createdAt);
    }

    /**
     * @param array<mixed> $content
     */
    public function setContent(array $content): void
    {
        $this->set('content', $content);
    }

    public function name(): string
    {
        return $this->getString('name');
    }

    public function createdAt(): null|string
    {
        return $this->getFormattedDateTime('created_at', "", format: "Y-m-d");
    }

    public function updatedAt(): null|string
    {
        return $this->getFormattedDateTime('updated_at', "", format: "Y-m-d");
    }

    public function setContentType(string $componentName): self
    {
        $this->defaultContentType = $componentName;
        return $this;
    }

    public function defaultContentType(): string
    {
        return $this->defaultContentType;
    }

    public function id(): string
    {
        return $this->getString('id');
    }

    public function uuid(): string
    {
        return $this->getString('uuid');
    }

    /**
     * Validates if the story data contains all required fields and valid values
     */
    public function isValid(): bool
    {
        if (!$this->hasKey('name') || in_array($this->getString('name'), ['', '0'], true)) {
            return false;
        }

        return $this->hasKey('slug') && !in_array($this->getString('slug'), ['', '0'], true);
    }
}
