<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

/**
 * User information returned by Storyblok's OAuth user_info endpoint.
 */
class OAuthUserInfo extends StoryblokData
{
    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    public function user(): User
    {
        return User::make($this->getArray("user"));
    }

    public function roles(): OAuthUserRoles
    {
        return OAuthUserRoles::make($this->getArray("roles"));
    }
}
