<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Storyblok\ManagementApi\StoryblokUtils;

final class StoryblokUtilsApiTest extends TestCase
{
    /**
     * @return \Iterator<string, array{(int | string), string}>
     */
    public static function regionFromSpaceIdProvider(): \Iterator
    {
        yield "EU string 1000" => ["1000", "EU"];
        yield "EU string 10000" => ["10000", "EU"];
        yield "US int 1000000" => [1_000_000, "US"];
    }

    #[DataProvider("regionFromSpaceIdProvider")]
    public function testGetRegionFromSpaceId(
        string|int $spaceId,
        string $region,
    ): void {
        $this->assertSame(
            $region,
            StoryblokUtils::getRegionFromSpaceId($spaceId),
        );
    }

    /**
     * @return \Iterator<string, array{string, string}>
     */
    public static function baseUriFromRegionForMapiProvider(): \Iterator
    {
        yield "EU region" => ["EU", "https://mapi.storyblok.com"];
        yield "US region" => ["US", "https://api-us.storyblok.com"];
        yield "invalid region" => ["NOTVALID", "https://mapi.storyblok.com"];
    }

    #[DataProvider("baseUriFromRegionForMapiProvider")]
    public function testBaseUriFromRegionForMapi(
        string $region,
        string $baseUri,
    ): void {
        $this->assertSame(
            $baseUri,
            StoryblokUtils::baseUriFromRegionForMapi($region),
        );
    }

    /**
     * @return \Iterator<string, array{(int | string), string}>
     */
    public static function planDescriptionProvider(): \Iterator
    {
        yield "development string 999" => ["999", "Development Plan"];
        yield "development int 999" => [999, "Development Plan"];
        yield "starter plan 1" => [1100, "Starter (Plan 1)"];
        yield "not listed returns code" => [12345, "12345"];
        yield "growth plan 2i" => [1200, "Growth (Plan 2i)"];
        yield "growth plus plan 2ii" => [1300, "Growth Plus (Plan 2ii)"];
        yield "premium plan 3" => [1400, "Premium (Plan 3)"];
        yield "premium cn plan 3 cn" => [1401, "Premium CN (Plan 3 CN)"];
        yield "elite plan 4" => [1500, "Elite (Plan 4)"];
        yield "elite cn plan 4 cn" => [1501, "Elite CN (Plan 4 CN)"];
    }

    #[DataProvider("planDescriptionProvider")]
    public function testGetPlanDescription(
        int|string $code,
        string $description,
    ): void {
        $this->assertSame(
            $description,
            StoryblokUtils::getPlanDescription($code),
        );
    }

    public function testNotValidSpaceId(): void
    {
        $this->assertSame("EU", StoryblokUtils::getRegionFromSpaceId(-222));
    }
}
