<?php

declare(strict_types=1);

namespace Storyblok\Mapi\Data;

use Storyblok\Mapi\StoryblokUtils;

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

    public function id(): string
    {
        return $this->getString('id');
    }

    public function userId(): string
    {
        return $this->getString('userid');
    }

    public function email(): string
    {
        return $this->getString('email');
    }



    public function hasOrganization(): bool
    {
        return $this->getBoolean('has_org');
    }

    public function hasPartner(): bool
    {
        return $this->getBoolean('has_partner');
    }

    public function lastSignInAt(): null|string
    {
        return $this->getFormattedDateTime('last_sign_in_at', "", format: "Y-m-d");
    }

    public function createdAt(): null|string
    {
        return $this->getFormattedDateTime('created_at', "", format: "Y-m-d");
    }

}
