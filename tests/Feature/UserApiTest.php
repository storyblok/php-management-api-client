<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Storyblok\ManagementApi\Data\User;
use Storyblok\ManagementApi\Endpoints\UserApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Component\HttpClient\MockHttpClient;

final class UserApiTest extends TestCase
{
    public function testCurrentUser(): void
    {
        $responses = [
            $this->mockResponse('one-user', 200),
            $this->mockResponse('empty-user', 404),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $userApi = new UserApi($mapiClient);

        $storyblokResponse = $userApi->me();
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression('/.*users.*/', $url);

        /** @var User $userData */
        $userData = $storyblokResponse->data();

        $this->assertSame('John', $userData->firstname());
        $this->assertSame('Doe', $userData->lastname());
        $this->assertSame('123456', $userData->id());
        $this->assertSame('Storyblok', $userData->orgName());
        $this->assertSame('myusername', $userData->username());
        $this->assertSame('admin', $userData->orgRole());
        $this->assertSame('myuserid', $userData->userId());
        $this->assertSame('test@test.com', $userData->email());
        $this->assertSame('2022-06-01 16:54:34', $userData->createdAt());
        $this->assertSame('2022-06-01', $userData->createdAt('Y-m-d'));
        $this->assertTrue($userData->hasOrganization());
        $this->assertTrue($userData->hasPartner());
        $this->assertSame('approved', $userData->partnerStatus());
        $this->assertSame('Europe/Rome', $userData->timezone());
        $this->assertSame('https://img2.storyblok.com/72x72/avatars/118830/01290bd7fa/myimage.JPG', $userData->avatarUrl());
        $this->assertSame('https://img2.storyblok.com/avatars/118830/01290bd7fa/myimage.JPG', $userData->avatarUrl(null));

        $userData = User::make([]);
        $this->assertInstanceOf(User::class, $userData);

        $userApi = new UserApi($mapiClient);
        $this->assertInstanceOf(UserApi::class, $userApi);
    }
}
