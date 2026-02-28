<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

class AppProvision extends BaseData
{
    public function __construct(
        string $name
    ) {
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
        if (!($dataObject->hasKey('name'))) {
            // is not valid
        }

        $appProvision = new self(
            $dataObject->getString("name")
        );
        $appProvision->setData($dataObject->toArray());
        // validate
        if (! $appProvision->isValid()) {
            throw new StoryblokFormatException("AppProvision is not valid");
        }

        return $appProvision;
    }

    public function isValid(): bool
    {
        return $this->hasKey('name');
    }

    public function name(): string
    {
        return $this->getString('name');
    }

    public function appId(): string
    {
        return $this->getString('app_id');
    }

    public function slug(): string
    {
        return $this->getString('slug');
    }

    public function planLevel(): int|null
    {
        return $this->getInt('plan_level');
    }

    public function inSidebar(): bool
    {
        return $this->getBoolean('in_sidebar', false);
    }

    public function inToolbar(): bool
    {
        return $this->getBoolean('in_toolbar', false);
    }

    public function enableSpaceSettings(): bool
    {
        return $this->getBoolean('enable_space_settings', false);
    }
}
