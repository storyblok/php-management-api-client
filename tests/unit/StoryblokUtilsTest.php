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
}
