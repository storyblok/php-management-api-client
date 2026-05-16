<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Data\StoryblokData;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;
use Storyblok\ManagementApi\StoryblokUtils;

class Space extends BaseData
{
    /**
     * @param string $name The space name. When provided, it is included in the
     *                     payload sent to the API. When omitted or empty, the
     *                     `name` field is not added, so the API leaves the
     *                     existing name untouched on update.
     */
    public function __construct(string $name = '')
    {
        $this->data = [];
        if ($name !== '') {
            $this->data["name"] = $name;
        }
    }

    /**
     * Creates a Space from a full API response array.
     *
     * Designed for hydrating Space objects returned by the Storyblok Management
     * API. The data array is expected to be a complete space representation
     * (i.e., it must contain a "name" key). The internal data is replaced in
     * full by the provided array.
     *
     * For partial updates where only specific fields should be sent,
     * use forUpdate() instead.
     *
     * @param mixed[] $data Full space data from the API response.
     * @throws StoryblokFormatException When the name field cannot be read back after hydration.
     */
    public static function make(array $data = []): self
    {
        $dataObject = new StoryblokData($data);
        $name = $dataObject->getString("name");
        $space = new self($dataObject->getString("name"));
        $space->setData($dataObject->toArray());
        // Sanity-check: name must survive the round-trip through setData()
        if ($space->name() !== $name) {
            throw new StoryblokFormatException("Space has no name");
        }

        return $space;
    }

    /**
     * Creates a Space containing only the specified fields for a partial update.
     *
     * Unlike new Space($name) — which always forces `name` into the payload — or
     * Space::make($data) — which is intended for full API response hydration —
     * this factory populates the payload with exactly and only the fields you
     * provide. No other fields (including `name`) are added automatically.
     *
     * The Storyblok Management API applies only the fields present in the request
     * body, leaving all other space settings untouched. This makes forUpdate()
     * the right choice whenever you need to change a subset of space settings.
     *
     * Example — update only the Dimensions app folder configuration:
     *
     *   $space = Space::forUpdate([
     *       'dimensions_app_folder_ids' => [123, 456],
     *       'dimensions_app_folders'    => [
     *           ['folder_id' => 123, 'ai_translation_code' => ''],
     *           ['folder_id' => 456, 'ai_translation_code' => 'it'],
     *       ],
     *   ]);
     *   $spaceApi->update($spaceId, $space);
     *
     * Example — update only the domain:
     *
     *   $space = Space::forUpdate(['domain' => 'https://new.example.com']);
     *   $spaceApi->update($spaceId, $space);
     *
     * @param array<string, mixed> $fields The fields to include in the update payload.
     */
    public static function forUpdate(array $fields): self
    {
        $space = new self('');
        $space->setData($fields);
        return $space;
    }

    public function setName(string $name): self
    {
        $this->set("name", $name);
        return $this;
    }

    public function setDomain(string $domain): self
    {
        $this->set("domain", $domain);
        return $this;
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
     */
    public function domain(): string
    {
        return $this->getString("domain");
    }

    /**
     * Retrieves the first preview access token associated with the Space.
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
     * Returns true if the given user is the owner of this space.
     */
    public function isOwnedByUser(User $user): bool
    {
        return $this->ownerId() === $user->id();
    }

    /**
     * Returns true if the space is marked as a demo/example space.
     */
    public function isDemo(): bool
    {
        return $this->getBoolean("is_demo", false);
    }

    /**
     * Clears the demo/example space flag.
     */
    public function removeDemoMode(): self
    {
        $this->set("is_demo", false);
        return $this;
    }
}
