<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use Roberto\Storyblok\Mapi\StoryblokUtils;

final class StoryblokUtilsTests extends TestCase
{
    public function testGetRegionFromSpaceId(): void
    {
        $region = StoryblokUtils::getRegionFromSpaceId(32000);

        $this->assertIsString($region);
        $this->assertSame("EU",$region, "The region is EU");


    }
}
