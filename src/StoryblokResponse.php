<?php

namespace Roberto\Storyblok\Mapi;

use Roberto\Storyblok\Mapi\Data\StoryblokData;
use Symfony\Contracts\HttpClient\ResponseInterface;

class StoryblokResponse implements StoryblokResponseInterface
{
    public function __construct(private readonly ResponseInterface $response) {}

    public static function make(ResponseInterface $response): StoryblokResponse
    {
        return new self($response);
    }

    public function getResponse(): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        return $this->response;
    }


    public function data(): StoryblokData
    {
        return StoryblokData::makeFromResponse($this);
    }

    public function getResponseBody(): string
    {
        return $this->response->getContent(false);
    }

    public function getResponseHeaders(): void
    {
        $this->response->getHeaders();
    }

    public function getResponseStatusCode(): int
    {
        return $this->response->getStatusCode();
    }
    public function getLastCalledUrl(): mixed
    {
        return $this->response->getInfo('url');
    }

    public function asJson(): void
    {
        // TODO: Implement asJson() method.
    }


    public function toArray(): array
    {
        return $this->response->toArray();
    }




}
