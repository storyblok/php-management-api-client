<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\StoryblokUtils;

class UserData extends StoryblokData
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
        return $this->getString('org.name');
    }

    public function username(): string
    {
        return $this->getString('username');
    }

    public function firstname(): string
    {
        return $this->getString('firstname');
    }

    public function lastname(): string
    {
        return $this->getString('lastname');
    }

    public function id(): string
    {
        return $this->getString('id');
    }

    public function orgRole(): string
    {
        return $this->getString('org_role');
    }

    public function userId(): string
    {
        return $this->getString('userid');
    }

    public function email(): string
    {
        return $this->getString('email');
    }

    public function createdAt(string $format = 'Y-m-d H:i:s'): string|null
    {
        return $this->getFormattedDateTime(
            'created_at',
            format: $format,
        );
    }

    public function hasOrganization(): bool
    {
        return $this->getBoolean('has_org');
    }

    public function hasPartner(): bool
    {
        return $this->getBoolean('has_partner');
    }

    public function partnerStatus(): string
    {
        return $this->getString('partner_status');
    }

    public function timezone(): string
    {
        return $this->getString('timezone');
    }

    public function avatarUrl(?int $size = 72): string
    {
        $sizeString = "";
        if (null !== $size) {
            $sizeString = $size . 'x' . $size . "/";
        }

        return "https://img2.storyblok.com/" . $sizeString . $this->getString('avatar');
    }
}
