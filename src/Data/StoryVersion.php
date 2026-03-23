<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

class StoryVersion extends BaseData
{
    public function __construct(
        int $storyId,
    ) {
        $this->data = [];
        $this->data['story_id'] = $storyId;
    }

    /**
     * @param mixed[] $data
     * @throws StoryblokFormatException
     */
    public static function make(array $data = []): self
    {
        $dataObject = new StoryblokData($data);

        $storyVersion = new StoryVersion(
            $dataObject->getIntStrict("story_id"),
        );
        $storyVersion->setData($dataObject->toArray());
        if (! $storyVersion->isValid()) {
            throw new StoryblokFormatException("StoryVersion is not valid");
        }

        return $storyVersion;
    }

    public function isValid(): bool
    {
        return $this->hasKey('story_id');
    }

    public function id(): string
    {
        return $this->getString('id');
    }

    public function storyId(): string
    {
        return $this->getString('story_id');
    }

    public function userId(): string
    {
        return $this->getString('user_id');
    }

    public function status(): string
    {
        return $this->getString('status');
    }

    public function releaseId(): string
    {
        return $this->getString('release_id');
    }

    public function parentId(): string
    {
        return $this->getString('parent_id');
    }

    public function createdAt(): string
    {
        return $this->getFormattedDateTime('created_at') ?? "";
    }

    public function firstname(): string
    {
        return $this->getString('user.firstname');
    }

    public function lastname(): string
    {
        return $this->getString('user.lastname');
    }

    public function friendlyName(): string
    {
        return $this->getString('user.friendly_name');
    }
}
