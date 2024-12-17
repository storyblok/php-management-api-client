<?php

namespace Roberto\Storyblok\Mapi;

use Symfony\Contracts\HttpClient\ResponseInterface;

interface StoryblokResponseInterface
{
    public function __construct(ResponseInterface $response);
    public static function make(ResponseInterface $response);
    public function getResponse();
    public function getResponseBody();
    public function getResponseHeaders();
    public function getResponseStatusCode();
    public function asJson();
    public function toArray();

}
