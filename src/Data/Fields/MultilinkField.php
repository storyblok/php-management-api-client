<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields;

use Storyblok\ManagementApi\Data\BaseData;

class MultilinkField extends BaseData implements FieldValueInterface
{
    public const TYPE = "multilink";

    public function __construct(
        string $linktype = "",
        string $url = "",
        string $id = "",
        string $cachedUrl = "",
    ) {
        $this->data = [
            "fieldtype" => self::TYPE,
            "id" => $id,
            "url" => $url,
            "cached_url" => $cachedUrl,
            "linktype" => $linktype,
            "anchor" => null,
            "target" => null,
        ];
    }

    public static function url(string $url): self
    {
        return new self("url", $url, cachedUrl: $url);
    }

    public static function email(string $email): self
    {
        return new self("email", $email);
    }

    public static function story(string $id, string $cachedUrl = ""): self
    {
        return new self("story", "", $id, $cachedUrl);
    }

    public static function asset(string $id, string $url = ""): self
    {
        return new self("asset", $url, $id);
    }

    public function id(): string
    {
        return $this->getString("id");
    }

    public function setId(string $id): static
    {
        $this->set("id", $id);
        return $this;
    }

    public function linkUrl(): string
    {
        return $this->getString("url");
    }

    public function setUrl(string $url): static
    {
        $currentUrl = $this->linkUrl();
        $this->set("url", $url);

        if (
            $this->linktype() === "url" &&
            in_array($this->cachedUrl(), ["", $currentUrl], true)
        ) {
            $this->setCachedUrl($url);
        }

        return $this;
    }

    public function cachedUrl(): string
    {
        return $this->getString("cached_url");
    }

    public function setCachedUrl(string $cachedUrl): static
    {
        $this->set("cached_url", $cachedUrl);
        return $this;
    }

    public function linktype(): string
    {
        return $this->getString("linktype");
    }

    public function setLinktype(string $linktype): static
    {
        $this->set("linktype", $linktype);
        return $this;
    }

    public function anchor(): ?string
    {
        return $this->getStringNullable("anchor");
    }

    public function setAnchor(?string $anchor): static
    {
        $this->set("anchor", $anchor);
        return $this;
    }

    public function target(): ?string
    {
        return $this->getStringNullable("target");
    }

    public function setTarget(?string $target): static
    {
        $this->set("target", $target);
        return $this;
    }

    public function openInNewTab(): static
    {
        return $this->setTarget("_blank");
    }

    public function openInSameTab(): static
    {
        return $this->setTarget("_self");
    }
}
