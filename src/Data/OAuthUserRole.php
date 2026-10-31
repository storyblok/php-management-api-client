<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

/**
 * Role data returned by Storyblok's OAuth user_info endpoint.
 */
class OAuthUserRole extends StoryblokData
{
    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    public function name(): string
    {
        return $this->getString("name");
    }
}
