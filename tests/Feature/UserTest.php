<?php

declare(strict_types=1);


use Storyblok\ManagementApi\Endpoints\UserApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Component\HttpClient\MockHttpClient;

test('Testing current user', function (): void {
    $responses = [
        \mockResponse("one-user", 200),
        \mockResponse("empty-user", 404),
        //\mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $userApi = new UserApi($mapiClient);

    $storyblokResponse = $userApi->me();
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*users.*/');
    /* @var \Storyblok\ManagementApi\Data\UserData $userData */
    $userData = $storyblokResponse->data();
    expect($userData->firstname())->toBe("John");
    expect($userData->lastname())->toBe("Doe");
    expect($userData->id())->toBe("123456");
    expect($userData->orgName())->toBe("Storyblok");
    expect($userData->username())->toBe("myusername");
    expect($userData->orgRole())->toBe("admin");
    expect($userData->userId())->toBe("myuserid");
    expect($userData->email())->toBe("test@test.com");
    expect($userData->email())->toBe("test@test.com");
    expect($userData->createdAt())->toBe("2022-06-01 16:54:34");
    expect($userData->createdAt("Y-m-d"))->toBe("2022-06-01");
    expect($userData->hasOrganization())->toBe(true);
    expect($userData->hasPartner())->toBe(true);
    expect($userData->partnerStatus())->toBe("approved");
    expect($userData->timezone())->toBe("Europe/Rome");
    expect($userData->avatarUrl())->toBe("https://img2.storyblok.com/72x72/avatars/118830/01290bd7fa/myimage.JPG");
    expect($userData->avatarUrl(null))->toBe("https://img2.storyblok.com/avatars/118830/01290bd7fa/myimage.JPG");

    $userData = \Storyblok\ManagementApi\Data\UserData::make([]);
    expect($userData)->toBeInstanceOf(\Storyblok\ManagementApi\Data\UserData::class);

    $userApi = $mapiClient->userApi();
    expect($userApi)->toBeInstanceOf(UserApi::class);



});
