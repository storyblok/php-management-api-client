<?php

declare(strict_types=1);

namespace Roberto\Storyblok\Mapi\Endpoints;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class EndpointBase
{
    public $lastResponseInfo;

    public function __construct(protected ?HttpClientInterface $clientMapi) {}



    public function makeRequest(
        string $method = "GET",
        string $path = "/v1/spaces",
        array $options = [],
    ) {

        $response = $this->clientMapi->request(
            $method,
            $path,
            $options,
        );
        $this->lastResponseInfo = $response->getInfo();

        return $response;
    }

    public function getLastCalledUrl(): string
    {
        if (! is_array($this->lastResponseInfo)) {
            return "";
        }
        if (array_key_exists("url", $this->lastResponseInfo)) {
            return $this->lastResponseInfo["url"];
        }
        return "";
    }
}
