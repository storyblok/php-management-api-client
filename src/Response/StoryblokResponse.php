<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Response;

use Storyblok\ManagementApi\Data\StoryblokData;
use Storyblok\ManagementApi\Data\StoryblokDataInterface;
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

    public function data(): StoryblokDataInterface
    {

        if (method_exists($this->dataClass, "makeFromResponse")) {
            return ($this->dataClass)::makeFromResponse($this->toArray());
        }

        return $this->dataClass::make($this->toArray());
    }

    public function getResponseBody(): string
    {
        return $this->response->getContent(false);
    }

    public function getResponseHeaders(): void
    {
        $this->response->getHeaders();
    }

    public function getHeader(string $headerName): mixed
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

    public function getHeaderInt(string $headerName): int|null
    {
        $value = $this->getHeader($headerName);
        return is_numeric($value) ? (int) $value : null;
    }

    public function total(): int|null
    {
        return  $this->getHeaderInt("total");
    }

    public function perPage(): int|null
    {
        return $this->getHeaderInt("per-page");
    }

    public function getResponseStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function getLastCalledUrl(): string
    {
        if (is_scalar($this->response->getInfo('url'))) {
            return strval($this->response->getInfo('url'));
        }

        return "";

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
        $message = $data->getString("error");
        if ($message !== '' && $message !== '0') {
            return $message;
        }

        $message = match ($this->getResponseStatusCode()) {
            400 => "Bad Request. Wrong format was sent (eg. XML instead of JSON).",
            401 => "Unauthorized. No valid API key provided.",
            403 => "Forbidden. Insufficient permissions.",
            404 => "Not Found. The requested resource doesn't exist (perhaps due to not yet published content entries).",
            422 => "Unprocessable Entity. The request cannot be processed because it is invalid, for example, due to a missing required parameter or a duplicate key.",
            429 => "Too many requests. Too many requests hit the API too quickly. We recommend an exponential backoff (throttling) of your requests.",
            500, 502, 503, 504 => "Server error. We are unable to process your request.",
            default => "Unknown error",
        };
        return $this->getResponseStatusCode() . " - " . $message;
    }

    public function asJson(): string|false
    {
        return json_encode($this->toArray());
    }

    /**
     * @return array<mixed>
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     */
    public function toArray(): array
    {

        return $this->response->toArray();
    }
}
