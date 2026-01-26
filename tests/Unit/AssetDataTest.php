<?php

declare(strict_types=1);

namespace Tests\Feature;

use Exception;
use Storyblok\ManagementApi\Data\Asset;
use Storyblok\ManagementApi\Data\Assets;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;
use Tests\TestCase;

final class AssetDataTest extends TestCase
{
    public function testAssetData(): void
    {
        $contentString = $this->mockData("one-asset");
        $content = json_decode($contentString, true);
        $this->assertIsArray($content);

        $asset = Asset::make($content);

        $this->assertSame(
            "https://s3.amazonaws.com/a.storyblok.com/f/222/3799x6005/3af265ee08/mypic.jpg",
            $asset->filename(),
        );
        $this->assertFalse($asset->isExternalUrl());
        $asset->setExternalUrl("https://storyblok.com/some.jpg");
        $this->assertSame("https://storyblok.com/some.jpg", $asset->filename());
        $this->assertTrue($asset->isExternalUrl());
    }

    public function testNotValidAssetData(): void
    {
        $this->expectException(StoryblokFormatException::class);
        $this->expectExceptionMessage("Asset is not valid");
        Asset::make([]);
    }

    public function testEmptyAssetData(): void
    {
        $asset = Asset::emptyAsset();
        $this->assertSame("", $asset->filename());
    }
}
