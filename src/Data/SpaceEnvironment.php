<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

class SpaceEnvironment extends BaseData
{
    public function __construct(string $name, string $location)
    {
        $this->data = [];
        $this->data["location"] = $location;
        $this->data["name"] = $name;
    }

    /**
     * @param mixed[] $data
     * @throws StoryblokFormatException
     */
    public static function make(array $data = []): self
    {
        $dataObject = new StoryblokData($data);
        if (!$dataObject->hasKey("name")) {
            throw new StoryblokFormatException(
                "Environments/Preview URL is not valid, missing the name",
            );
        }

        if (!$dataObject->hasKey("location")) {
            throw new StoryblokFormatException(
                "Environments/Preview URL is not valid, missing the location/URL",
            );
        }

        return new self(
            $dataObject->getString("name"),
            $dataObject->getString("location"),
        );
    }

    public function setName(string $name): void
    {
        $this->set("name", $name);
    }

    /**
     * Environment/Preview URL name
     */
    public function name(): string
    {
        return $this->getString("name");
    }

    public function setLocation(string $location): void
    {
        $this->set("location", $location);
    }

    /**
     * Environment/Preview URL location
     */
    public function location(): string
    {
        return $this->getString("location");
    }

    /**
     * Validates if the Environment/Preview URL data contains all required fields and valid values
     */
    public function isValid(): bool
    {
        return $this->hasKey("name") && $this->hasKey("location");
    }
}
