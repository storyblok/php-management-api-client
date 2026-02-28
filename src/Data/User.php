<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\StoryblokUtils;

class User extends StoryblokData
{
    /**
     * @param array<string, array<mixed>> $data
     */
    public static function makeFromResponse(array $data = []): self
    {
        return new self($data["user"] ?? []);
    }

    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    public function orgName(): string
    {
        return $this->getString("org.name");
    }

    public function username(): string
    {
        return $this->getString("username");
    }

    public function firstname(): string
    {
        return $this->getString("firstname");
    }

    public function lastname(): string
    {
        return $this->getString("lastname");
    }

    public function id(): string
    {
        return $this->getString("id");
    }

    public function orgRole(): string
    {
        return $this->getString("org_role");
    }

    public function userId(): string
    {
        return $this->getString("userid");
    }

    public function email(): string
    {
        return $this->getString("email");
    }

    public function createdAt(string $format = "Y-m-d H:i:s"): string|null
    {
        return $this->getFormattedDateTime("created_at", format: $format);
    }

    public function hasOrganization(): bool
    {
        return $this->getBoolean("has_org");
    }

    public function hasPartner(): bool
    {
        return $this->getBoolean("has_partner");
    }

    public function partnerStatus(): string
    {
        return $this->getString("partner_status");
    }

    public function timezone(): string
    {
        return $this->getString("timezone");
    }

    public function friendlyName(): string
    {
        return $this->getString("friendly_name");
    }

    public function altEmail(): string|null
    {
        return $this->getStringNullable("alt_email");
    }

    public function phone(): string|null
    {
        return $this->getStringNullable("phone");
    }

    public function lang(): string
    {
        return $this->getString("lang");
    }

    public function loginStrategy(): string
    {
        return $this->getString("login_strategy");
    }

    public function jobRole(): string
    {
        return $this->getString("job_role");
    }

    public function partnerRole(): string
    {
        return $this->getString("partner_role");
    }

    public function isEditor(): bool
    {
        return $this->getBoolean("is_editor");
    }

    public function isSso(): bool
    {
        return $this->getBoolean("sso");
    }

    public function avatarUrl(?int $size = 72): string
    {
        if ($this->getString("avatar") === "") {
            return "";
        }

        $sizeString = "";
        if (null !== $size) {
            $sizeString = $size . "x" . $size . "/";
        }

        return "https://img2.storyblok.com/" .
            $sizeString .
            $this->getString("avatar");
    }
}
