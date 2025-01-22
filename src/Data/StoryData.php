<?php

declare(strict_types=1);

namespace Storyblok\Mapi\Data;

use Storyblok\Mapi\Data\StoryblokData;

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

    public function getContentType(): string
    {
        return $this->defaultContentType;
    }



}
