<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;

class Collaborator extends BaseData
{
    public function __construct(
        string $role
    ) {
        $this->data = [];
        $this->data['role'] = $role;
    }

    /**
     * @param mixed[] $data
     * @throws StoryblokFormatException
     */
    public static function make(array $data = []): self
    {
        $dataObject = new StoryblokData($data);

        $collaborator = new Collaborator(
            $dataObject->getString("role")
        );
        $collaborator->setData($dataObject->toArray());
        if (! $collaborator->isValid()) {
            throw new StoryblokFormatException("Collaborator is not valid");
        }

        return $collaborator;
    }

    public function isValid(): bool
    {
        return $this->hasKey('role');
    }

    public function id(): string
    {
        return $this->getString('id');
    }

    public function userId(): string
    {
        return $this->getString('userid');
    }

    public function role(): string
    {
        return $this->getString('role');
    }

    public function spaceId(): string
    {
        return $this->getString('space_id');
    }

    public function spaceRoleId(): string
    {
        return $this->getString('space_role_id');
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

    public function realEmail(): string
    {
        return $this->getString('user.real_email');
    }
}
