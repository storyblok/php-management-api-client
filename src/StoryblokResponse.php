<?php

declare(strict_types=1);

namespace Roberto\Storyblok\Mapi;

use Roberto\Storyblok\Mapi\Data\StoryblokData;
use Roberto\Storyblok\Mapi\Data\StoryData;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class StoryblokResponse implements StoryblokResponseInterface
{
    public function __construct(
        private readonly ResponseInterface $response,
        private readonly string $dataClass = StoryblokData::class,
    ) {}

    public static function make(
        ResponseInterface $response,
        string $dataClass = StoryblokData::class,
    ): StoryblokResponse {

        return new self($response, $dataClass);
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }


    public function data(): StoryblokData
    {

        if (method_exists($this->dataClass, "makeFromResponse")) {
            return ($this->dataClass)::makeFromResponse($this->toArray());
        }

        return new ($this->dataClass)($this->toArray());

    }


    public function getResponseBody(): string
    {
        return $this->response->getContent(false);
    }

    public function getResponseHeaders(): void
    {
        $this->response->getHeaders();
    }

    public function getHeader($headerName): mixed
    {
        try {
            $headers = $this->response->getHeaders();

            if (array_key_exists($headerName, $headers) && array_key_exists(0, $headers[$headerName])) {
                return $headers[$headerName][0];
            }
        } catch (ClientExceptionInterface) {

        }

        return null;
    }

    public function total(): mixed
    {
        return $this->getHeader("total");
    }

    public function perPage(): mixed
    {
        return $this->getHeader("per-page");
    }

    public function getResponseStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function getLastCalledUrl(): mixed
    {
        return $this->response->getInfo('url');
    }

    public function isOk(): bool
    {
        return $this->getResponseStatusCode() >= 200 && $this->getResponseStatusCode() < 300;
    }

    public function getErrorMessage(): string
    {
        if ($this->isOk()) {
            return "No error detected, HTTP Status Code: " . $this->getResponseStatusCode();
        }

        $data = $this->data();
        $message = $data->get("error");
        return $message ?? "Unknown error, " . $this->getResponseStatusCode();
    }



    public function asJson(): void
    {
        // TODO: Implement asJson() method.
    }


    public function toArray(): array
    {

        return $this->response->toArray(false);
    }




}
