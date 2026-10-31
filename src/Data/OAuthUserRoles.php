<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

/**
 * Iterable collection of OAuth user roles.
 *
 * Iterating over this collection yields OAuthUserRole instances. The raw
 * response array remains available through toArray().
 */
class OAuthUserRoles extends StoryblokData
{
    /**
     * @return class-string<OAuthUserRole>
     */
    #[\Override]
    public function getDataClass(): string
    {
        return OAuthUserRole::class;
    }

    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    #[\Override]
    public function current(): OAuthUserRole
    {
        /** @var OAuthUserRole $current */
        $current = parent::current();

        return $current;
    }
}
