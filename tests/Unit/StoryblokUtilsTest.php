<?php

declare(strict_types=1);

use Storyblok\ManagementApi\StoryblokUtils;

test('Testing getRegionFromSpaceId', function (string|int $spaceId, $region): void {
    expect(StoryblokUtils::getRegionFromSpaceId($spaceId))->toBe($region);
})->with([
    ['1000', 'EU'],
    ['10000', 'EU'],
    [1_000_000, 'US'],
]);

test('Testing baseUriFromRegionForMapi', function (string $region, $baseUri): void {
    expect(StoryblokUtils::baseUriFromRegionForMapi($region))->toBe($baseUri);
})->with([
    ['EU', 'https://mapi.storyblok.com'],
    ['US', 'https://api-us.storyblok.com'],
    ['NOTVALID', 'https://mapi.storyblok.com'],

]);

test('Testing getPlanDescription', function (int|string $code, $description): void {
    expect(StoryblokUtils::getPlanDescription($code))->toBe($description);
})->with([
    ['999', 'Development Plan'],
    [999, 'Development Plan'],
    [12345, '12345'], // not listed, returning just the code
    [1200, 'Growth (Plan 2i)'],
    [1300, 'Growth Plus (Plan 2ii)'],
    [1400, 'Premium (Plan 3)'],
    [1401, 'Premium CN (Plan 3 CN)'],
    [1500, 'Elite (Plan 4)'],
    [1501, 'Elite CN (Plan 4 CN)'],
]);
