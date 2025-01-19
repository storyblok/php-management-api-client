<?php


declare(strict_types=1);

use Storyblok\Mapi\StoryblokUtils;

it('getPlanDescription', function (int $code, string $description): void {
    expect(StoryblokUtils::getPlanDescription($code))->toBe($description);
})->with([
    [0, 'Starter (Trial)'],
    [2, 'Pro Space'],
    [1, 'Standard Space'],
    [1000, 'Development'],
    [100, 'Community'],
    [200 , 'Entry'],
    [300 , 'Teams'],
    [301 , 'Business'],
    [400 , 'Enterprise'],
    [500 , 'Enterprise Plus'],
    [501 , 'Enterprise Essentials'],
    [502 , 'Enterprise Scale'],
    [503 , 'Enterprise Ultimate'],
]);

it('baseUriFromRegionForMapi', function (string $region, string $url): void {
    expect(StoryblokUtils::baseUriFromRegionForMapi($region))->toBe($url);
})->with([
    ["US", "https://api-us.storyblok.com"],
    [ "CA", "https://api-ca.storyblok.com"],
    [ "AP", "https://api-ap.storyblok.com"],
    [ "CN", "https://app.storyblokchina.cn"],
    [ "EU", "https://mapi.storyblok.com"]
]);


it('baseUriFromRegionForOauth', function (string $region, string $url): void {
    expect(StoryblokUtils::baseUriFromRegionForOauth($region))->toBe($url);
})->with([
    ["EU", "https://api.storyblok.com"],
    ["US", "https://api-us.storyblok.com"],
    [ "CA", "https://api-ca.storyblok.com"],
    [ "AP", "https://api-ap.storyblok.com"],
    [ "CN", "https://app.storyblokchina.cn"]
]);
