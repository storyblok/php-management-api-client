<?php

declare(strict_types=1);

namespace unit;

use PHPUnit\Framework\TestCase;
use Roberto\Storyblok\Mapi\StoryblokUtils;

final class StoryblokUtilsTest extends TestCase
{
    public function testGetRegionFromSpaceId(): void
    {
        $region = StoryblokUtils::getRegionFromSpaceId(32000);
        $this->assertSame("EU", $region, "The region is EU");
        $region = StoryblokUtils::getRegionFromSpaceId(1_000_000);
        $this->assertSame("US", $region, "The region is US");
    }
    public function testBaseUriFromRegion(): void
    {
        $baseUri = StoryblokUtils::baseUriFromRegionForMapi("EU");
        $this->assertSame("https://mapi.storyblok.com", $baseUri, "The base uri for EU");
        $baseUri = StoryblokUtils::baseUriFromRegionForMapi("US");
        $this->assertSame("https://api-us.storyblok.com", $baseUri, "The base uri for US");
        $baseUri = StoryblokUtils::baseUriFromRegionForMapi("AP");
        $this->assertSame("https://api-ap.storyblok.com", $baseUri, "The base uri for Australia");
        $baseUri = StoryblokUtils::baseUriFromRegionForMapi("CA");
        $this->assertSame("https://api-ca.storyblok.com", $baseUri, "The base uri for Canada");

    }
}
