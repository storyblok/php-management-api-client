<?php
declare(strict_types=1);
namespace Roberto\Storyblok\Mapi\Endpoints;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class EndpointBase {
    public function __construct(protected ?HttpClientInterface $clientMapi) {}


    public function makeRequest(
        string $method = "GET",
        string $path = "/spaces",
        array $options = [],
    ){
        return $this->clientMapi->request(
            $method,
            $path,
            $options,
        );
    }
}

