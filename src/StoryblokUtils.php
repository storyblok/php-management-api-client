<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi;

class StoryblokUtils
{
    public static function getRegionFromSpaceId(string|int $spaceId): string
    {
        return ($spaceId >= 1_000_000) ? "US" : "EU";
    }

    /**
     * Each type of storyblok service plan
     * (Community, Business, Enterprise, etc.) is internally coded by
     * an integer number.
     * The function returns the formal description of the plan
     * related to the code.
     */
    public static function getPlanDescription(int|string $planLevel): string
    {
        return (string) match ($planLevel) {

            0, "0" => 'Starter (Trial)',
            2, "2" => 'Pro Space',
            1,"1" => 'Standard Space',
            1000, "1000" => 'Development',
            100, "100" => 'Community',
            200, "200" => 'Entry',
            300, "300" => 'Teams',
            301, "301" => 'Business',
            400, "400" => 'Enterprise',
            500, "500" => 'Enterprise Plus',
            501, "501" => 'Enterprise Essentials',
            502, "502" => 'Enterprise Scale',
            503, "503" => 'Enterprise Ultimate',

            default => $planLevel,
        };

    }

    public static function baseUriFromRegionForMapi(string $region): string
    {
        return match ($region) {
            "US" => "https://api-us.storyblok.com",
            "CA" => "https://api-ca.storyblok.com",
            "AP" => "https://api-ap.storyblok.com",
            "CN" => "https://app.storyblokchina.cn",
            default => "https://mapi.storyblok.com",
        };
    }

    public static function baseUriFromRegionForOauth(string $region): string
    {
        return match ($region) {
            "US" => "https://api-us.storyblok.com",
            "CA" => "https://api-ca.storyblok.com",
            "AP" => "https://api-ap.storyblok.com",
            "CN" => "https://app.storyblokchina.cn",
            default => "https://api.storyblok.com",
        };
    }
}
