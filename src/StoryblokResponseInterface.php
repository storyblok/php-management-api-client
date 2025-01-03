<?php

declare(strict_types=1);

namespace Storyblok\Mapi;

use Storyblok\Mapi\Data\StoryblokData;
use Symfony\Contracts\HttpClient\ResponseInterface;

interface StoryblokResponseInterface
{
    public function __construct(ResponseInterface $response);

    public static function make(ResponseInterface $response): StoryblokResponse;

    public function getResponse(): ResponseInterface;

    public function getResponseBody(): string;

    public function getErrorMessage(): string;

    public function total(): mixed;

    public function perPage(): mixed;

    public function getResponseHeaders(): void;

    public function getResponseStatusCode(): int;

    public function asJson(): string;

    /**
     * @return array<mixed>
     */
    public function toArray(): array;

    public function data(): StoryblokData;

    public function getLastCalledUrl(): mixed;

    public function isOk(): bool;

}
