<?php

declare(strict_types=1);

namespace Tests\Feature;

use Exception;
use Storyblok\ManagementApi\Data\Assets;
use Tests\TestCase;

final class AssetsDataTest extends TestCase
{
    public function testAssetsData(): void
    {
        $contentString = $this->mockData("list-assets");
        $content = json_decode($contentString, true);
        // chewck if is array
        $this->assertIsArray($content);
        $this->assertArrayHasKey("assets", $content);
        $assets = Assets::make($content["assets"]);

        $this->assertCount(2, $assets);
        $assets = Assets::makeFromResponse($content);
        $this->assertCount(2, $assets);
    }

    public function testAssetsGetIds(): void
    {
        $contentString = $this->mockData("list-assets");
        $content = json_decode($contentString, true);
        // chewck if is array
        $this->assertIsArray($content);
        $this->assertArrayHasKey("assets", $content);
        $assets = Assets::make($content["assets"]);

        $this->assertCount(2, $assets);
        $ids = $assets->getIds();
        $this->assertCount(2, $ids);
    }
}
