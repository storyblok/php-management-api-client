<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Storyblok\ManagementApi\StoryblokUtils;

final class StoryblokUtilsApiTest extends TestCase
{
    /**
     * @return \Iterator<string, array{int, string}>
     */
    public static function planDescriptionProvider(): \Iterator
    {
        yield 'starter trial' => [0, 'Starter (Trial)'];
        yield 'pro space' => [2, 'Pro Space'];
        yield 'standard space' => [1, 'Standard Space'];
        yield 'development' => [1000, 'Development'];
        yield 'community' => [100, 'Community'];
        yield 'entry' => [200, 'Entry'];
        yield 'teams' => [300, 'Teams'];
        yield 'business' => [301, 'Business'];
        yield 'enterprise' => [400, 'Enterprise'];
        yield 'enterprise plus' => [500, 'Enterprise Plus'];
        yield 'enterprise essentials' => [501, 'Enterprise Essentials'];
        yield 'enterprise scale' => [502, 'Enterprise Scale'];
        yield 'enterprise ultimate' => [503, 'Enterprise Ultimate'];
    }

    #[DataProvider('planDescriptionProvider')]
    public function testGetPlanDescription(int $code, string $description): void
    {
        $this->assertSame($description, StoryblokUtils::getPlanDescription($code));
    }

    /**
     * @return \Iterator<string, array{string, string}>
     */
    public static function baseUriFromRegionForMapiProvider(): \Iterator
    {
        yield 'US region' => ['US', 'https://api-us.storyblok.com'];
        yield 'CA region' => ['CA', 'https://api-ca.storyblok.com'];
        yield 'AP region' => ['AP', 'https://api-ap.storyblok.com'];
        yield 'CN region' => ['CN', 'https://app.storyblokchina.cn'];
        yield 'EU region' => ['EU', 'https://mapi.storyblok.com'];
    }

    #[DataProvider('baseUriFromRegionForMapiProvider')]
    public function testBaseUriFromRegionForMapi(string $region, string $url): void
    {
        $this->assertSame($url, StoryblokUtils::baseUriFromRegionForMapi($region));
    }

    /**
     * @return \Iterator<string, array{string, string}>
     */
    public static function baseUriFromRegionForOauthProvider(): \Iterator
    {
        yield 'EU region' => ['EU', 'https://api.storyblok.com'];
        yield 'US region' => ['US', 'https://api-us.storyblok.com'];
        yield 'CA region' => ['CA', 'https://api-ca.storyblok.com'];
        yield 'AP region' => ['AP', 'https://api-ap.storyblok.com'];
        yield 'CN region' => ['CN', 'https://app.storyblokchina.cn'];
    }

    #[DataProvider('baseUriFromRegionForOauthProvider')]
    public function testBaseUriFromRegionForOauth(string $region, string $url): void
    {
        $this->assertSame($url, StoryblokUtils::baseUriFromRegionForOauth($region));
    }
}
