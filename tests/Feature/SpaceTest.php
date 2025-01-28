<?php

declare(strict_types=1);

use Storyblok\ManagementApi\ManagementApiClient;
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
                    "limits" => [],
                    "created_at" => "2018-11-10T15:33:18.402Z",
                    "id" => 680,
                    "role" => "admin",
                    "owner_id" => 1114,
                    "story_published_hook" => null,
                ],
            ])
            , ['http_code' => 200]),
        new MockResponse(json_encode(["This record could not be found"])
            , ['http_code' => 404]),

    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $spaceApi = $mapiClient->spaceApi();

    $storyblokResponse = $spaceApi->get("111");
    /** @var \Roberto\Storyblok\Mapi\Data\SpaceData $storyblokData */
    $storyblokData =  $storyblokResponse->data();
    expect($storyblokData->get("name"))
        ->toBe("Example Space")
        ->and($storyblokData->name())->toBe("Example Space")
        ->and($storyblokData->createdAt())->toBe("2018-11-10")
        ->and($storyblokData->planDescription())->toBe("Starter (Trial)")
    ->and($storyblokResponse->getResponseStatusCode())->toBe(200);

    $storyblokResponse = $spaceApi->get("111notexists");
    /** @var \Roberto\Storyblok\Mapi\Data\SpaceData $storyblokData */
    expect( $storyblokResponse->getResponseStatusCode())->toBe(404) ;
    expect( $storyblokResponse->asJson())->toBe('["This record could not be found"]');

    $storyblokData->setName("New Name");
    expect($storyblokData->get("name"))
        ->toBe("New Name")
        ->and($storyblokData->createdAt())->toBe("2018-11-10")
        ->and($storyblokData->get("domain"))->toBe("https://example.storyblok.com");

    $storyblokData->setDomain("example.com");
    expect($storyblokData->get("name"))
        ->toBe("New Name")
        ->and($storyblokData->createdAt())->toBe("2018-11-10")
        ->and($storyblokData->get("domain"))->toBe("example.com");
});


test('Testing multiple spaces, SpaceData', function (): void {
    $responses = [
        new MockResponse(json_encode([
                "spaces" =>
                    [
                        [
                            "name" => "Example Space",
                            "domain" => "https://example.storyblok.com",
                            "uniq_domain" => null,
                            "plan" => "starter",
                            "plan_level" => 0,
                            "limits" => [],
                            "created_at" => "2018-11-10T15:33:18.402Z",
                            "id" => 680,
                            "role" => "admin",
                            "owner_id" => 1114,
                            "story_published_hook" => null,
                        ],
                        [
                            "name" => "Example Space2",
                            "domain" => "https://example.storyblok.com",
                            "uniq_domain" => null,
                            "plan" => "starter",
                            "plan_level" => 0,
                            "limits" => [],
                            "created_at" => "2018-11-10T15:33:18.402Z",
                            "id" => 680,
                            "role" => "admin",
                            "owner_id" => 1114,
                            "story_published_hook" => null,
                        ],

                    ]
            ])
            , ['http_code' => 200]),
        new MockResponse(json_encode(["This record could not be found"])
            , ['http_code' => 404]),

    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $spaceApi = $mapiClient->spaceApi();

    $storyblokResponse = $spaceApi->all();
    /** @var Storyblok\ManagementApi\Data\SpacesData $storyblokData */
    $storyblokData = $storyblokResponse->data();
    expect($storyblokData->get("0.name"))
        ->toBe("Example Space")
        ->and($storyblokResponse->getResponseStatusCode())->toBe(200);
    foreach ($storyblokData as $spaceItem) {
        expect($spaceItem->name())
            ->toBeString();
    }

    expect($storyblokData->howManySpaces())->toBe(2);

});
