<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Response;

use Storyblok\ManagementApi\Data\Space;
use Storyblok\ManagementApi\Data\Tag;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class TagResponse extends StoryblokResponse implements StoryblokResponseInterface
{
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws StoryblokFormatException
     * @throws ClientExceptionInterface
     */
    #[\Override]
    public function data(): Tag
    {
        $key = "tag";
        $array = $this->toArray();
        if (array_key_exists($key, $array)) {
            return Tag::make($array[$key]);
        }

        throw new StoryblokFormatException(sprintf("Expected '%s' in the response.", $key));
    }
}
