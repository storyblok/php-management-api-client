<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Data\StoryblokData;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;
use Storyblok\ManagementApi\StoryblokUtils;

class Space extends BaseData
{
    /**
     * @param string $name the space name
     */
    public function __construct(string $name)
    {
        $this->data = [];
        $this->data["name"] = $name;
    }

    /**
     * @param mixed[] $data
     * @throws StoryblokFormatException
     */
    public static function make(array $data = []): self
    {
        $dataObject = new StoryblokData($data);
        $name = $dataObject->getString("name");
        $space = new self($dataObject->getString("name"));
        $space->setData($dataObject->toArray());
        // validate
        if ($space->name() !== $name) {
            throw new StoryblokFormatException("Space has no name");
        }

        return $space;
    }

    public function setName(string $name): void
    {
        $this->set("name", $name);
    }

    public function setDomain(string $domain): void
    {
        $this->set("domain", $domain);
    }

    public function name(): string
    {
        return $this->getString("name", "");
    }

    public function region(): string
    {
        return $this->getString("region", "");
    }

    public function id(): string
    {
        return $this->getString("id", "");
    }

    /**
     * Retrieves the domain associated with the Space.
     *
     * Returns the value stored under the "domain" key as a string.
     *
     * @return string The domain name.
     */
    public function domain(): string
    {
        return $this->getString("domain");
    }

    /**
     * Retrieves the first token associated with the Space.
     *
     * Returns the value stored under the "first_token" key.
     *
     * @return string The first token, or an empty string if none is defined.
     */
    public function firstToken(): string
    {
        return $this->getString("first_token", "");
    }

    public function environments(): SpaceEnvironments
    {
        return SpaceEnvironments::make($this->getArray("environments"));
    }

    public function addEnvironment(
        SpaceEnvironment $spaceEnvironment,
    ): SpaceEnvironments {
        $environments = $this->getArray("environments");
        $environments[] = $spaceEnvironment->toArray();
        $this->set("environments", $environments);

        return SpaceEnvironments::make($environments);
    }

    public function createdAt(): null|string
    {
        return $this->getFormattedDateTime("created_at", "", format: "Y-m-d");
    }

    public function updatedAt(): null|string
    {
        return $this->getFormattedDateTime("updated_at", "", format: "Y-m-d");
    }

    public function planLevel(): string
    {
        return $this->getString("plan_level");
    }

    public function planDescription(): null|string
    {
        return StoryblokUtils::getPlanDescription($this->planLevel());
    }

    public function ownerId(): string
    {
        return $this->getString("owner_id", "");
    }

    /**
     * Determines whether the current entity is owned by the given user.
     *
     * Compares the space's owner ID with the ID of the provided user instance.
     *
     * @param User $user The user to check ownership against.
     * @return bool True if the user owns the entity, false otherwise.
     */
    public function isOwnedByUser(User $user): bool
    {
        return $this->ownerId() === $user->id();
    }

    /**
     * Checks whether the entity is marked as a demo instance.
     *
     * Retrieves the `is_demo` flag and returns its boolean value.
     *
     * @return bool True if the entity is marked as a demo, false otherwise.
     */
    public function isDemo(): bool
    {
        return $this->getBoolean("is_demo", false);
    }

    /**
     * Remove the Demo mode flag
     */
    public function removeDemoMode(): void
    {
        $this->set("is_demo", false);
    }
}
