<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

class App extends BaseData
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

        $app = new self(
            $dataObject->getString("name")
        );
        $app->setData($dataObject->toArray());
        // validate
        if (! $app->isValid()) {
            throw new StoryblokFormatException("App is not valid");
        }

        return $app;
    }

    public function isValid(): bool
    {
        return $this->hasKey('name');
    }

    public function name(): string
    {
        return $this->getString('name');
    }

    public function id(): string
    {
        return $this->getString('id');
    }

    public function slug(): string
    {
        return $this->getString('slug');
    }

    public function icon(): string
    {
        return $this->getString('icon');
    }

    public function description(): string
    {
        return $this->getString('description');
    }

    public function intro(): string
    {
        return $this->getString('intro');
    }

    public function status(): string
    {
        return $this->getString('status');
    }

    public function author(): string
    {
        return $this->getString('author');
    }

    public function website(): string
    {
        return $this->getString('website');
    }

    public function planLevel(): int|null
    {
        return $this->getInt('plan_level');
    }

    public function updatedAt(): string
    {
        return $this->getString('updated_at');
    }

    public function inSidebar(): bool
    {
        return $this->getBoolean('in_sidebar', false);
    }

    public function inToolbar(): bool
    {
        return $this->getBoolean('in_toolbar', false);
    }
}
