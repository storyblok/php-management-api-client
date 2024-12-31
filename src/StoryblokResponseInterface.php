<?php

declare(strict_types=1);

namespace Roberto\Storyblok\Mapi;

use Roberto\Storyblok\Mapi\Data\StoryblokData;
use Symfony\Contracts\HttpClient\ResponseInterface;

interface StoryblokResponseInterface
{
    public function __construct(ResponseInterface $response);

    public static function make(ResponseInterface $response);

    public function getResponse();

    public function getResponseBody();

    public function getErrorMessage(): string;

    public function total(): mixed;

    public function perPage(): mixed;

    public function getResponseHeaders();

    public function getResponseStatusCode();

    public function asJson();

    public function toArray();

    public function data(): StoryblokData;

    public function getLastCalledUrl(): mixed;

    public function isOk(): bool;

}
