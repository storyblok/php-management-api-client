<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

class Message extends BaseData
{
    /**
     * @param array<mixed> $data The initial data to store in the object.
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Factory method to create a new instance of Message.
     *
     * @param array<mixed> $data The data to initialize the object with.
     * @return Message A new instance of Message.
     */
    public static function make(array $data = []): Message
    {
        return new Message($data);
    }

    /**
     * Get the message string.
     */
    public function message(): string
    {
        return $this->getString("message");
    }
}
