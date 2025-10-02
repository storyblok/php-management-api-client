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
        $this->data['name'] = $name;
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
        $this->set('name', $name);
    }

    public function setDomain(string $domain): void
    {
        $this->set('domain', $domain);
    }

    public function name(): string
    {
        return $this->getString('name', "");
    }

    public function region(): string
    {
        return $this->getString('region', "");
    }

    public function id(): string
    {
        return $this->getString('id', "");
    }

    public function createdAt(): null|string
    {
        return $this->getFormattedDateTime('created_at', "", format: "Y-m-d");
    }

    public function planLevel(): string
    {
        return $this->getString('plan_level');
    }

    public function planDescription(): null|string
    {
        return StoryblokUtils::getPlanDescription($this->planLevel());
    }

    public function ownerId(): string
    {
        return $this->getString('owner_id', "");
    }
}
