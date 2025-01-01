<?php

declare(strict_types=1);

namespace Roberto\Storyblok\Mapi\Data;

use Roberto\Storyblok\Mapi\StoryblokUtils;

class UserData extends StoryblokData
{
    /**
     * @param array<mixed> $data
     */
    public static function makeFromResponse(array $data = []): self
    {
        return new self($data["user"] ?? []);
    }

    public function orgName(): null|string
    {
        return $this->get('org.name', "");
    }

    public function id(): int|string
    {
        return $this->get('id', "");
    }

    public function userId(): string
    {
        return $this->get('userid', "");
    }

    public function email(): string
    {
        return $this->get('email', "");
    }



    public function hasOrganization(): bool
    {
        return $this->get('has_org', false);
    }

    public function hasPartner(): bool
    {
        return $this->get('has_partner', false);
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
