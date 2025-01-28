<?php


declare(strict_types=1);

use Storyblok\ManagementApi\StoryblokUtils;

test('Testing getRegionFromSpaceId', function ($spaceId, $region): void {
    expect(StoryblokUtils::getRegionFromSpaceId($spaceId))->toBe($region);
})->with([
    ['1000', 'EU'],
    ['10000', 'EU'],
    [100_000_000, 'US'],
]);

test('Testing baseUriFromRegionForMapi', function ($region, $baseUri): void {
    expect(StoryblokUtils::baseUriFromRegionForMapi($region))->toBe($baseUri);
})->with([
    ['EU', 'https://mapi.storyblok.com'],
    ['US', 'https://api-us.storyblok.com'],
    ['NOTVALID', 'https://mapi.storyblok.com'],

]);
