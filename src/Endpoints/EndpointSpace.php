<?php

declare(strict_types=1);

namespace Roberto\Storyblok\Mapi\Endpoints;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class EndpointSpace extends EndpointBase
{
    public function __construct(
        protected ?HttpClientInterface $httpClient,
        protected string $spaceId,
    ) {
        parent::__construct($httpClient);
    }


}
