<?php

declare(strict_types=1);

use Roberto\Storyblok\Mapi\MapiClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\MockHttpClient;


test('Testing One space, SpaceData', function (): void {
    $responses = [
        new MockResponse(json_encode([
                "space" => [
                    "name" => "Example Space",
                    "domain" => "https://example.storyblok.com",
                    "uniq_domain" => null,
                    "plan" => "starter",
                    "plan_level" => 0,
                    "limits" => []
                ],
                "created_at" => "2018-11-10T15:33:18.402Z",
                "id" => 680,
                "role" => "admin",
                "owner_id" => 1114,
                "story_published_hook" => null,
            ])
            , []),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = MapiClient::initTest($client);
    $spaceApi = $mapiClient->spaceApi();

    $storyblokResponse = $spaceApi->get("111");
    /** @var \Roberto\Storyblok\Mapi\Data\SpaceData $storyblokData */
    $storyblokData =  $storyblokResponse->data();
    expect($storyblokData->get("name"))
        ->toBe("Example Space")
        ->and($storyblokData->name())->toBe("Example Space")
        ->and($storyblokData->createdAt())->toBe("2024-12-31")
        ->and($storyblokData->planDescription())->toBe("Starter (Trial)");

    $storyblokData->setName("New Name");
    expect($storyblokData->get("name"))
        ->toBe("New Name")
        ->and($storyblokData->createdAt())->toBe("2024-12-31")
        ->and($storyblokData->get("domain"))->toBe("https://example.storyblok.com");

    $storyblokData->setDomain("example.com");
    expect($storyblokData->get("name"))
        ->toBe("New Name")
        ->and($storyblokData->createdAt())->toBe("2024-12-31")
        ->and($storyblokData->get("domain"))->toBe("example.com");
});
