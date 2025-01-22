<?php

declare(strict_types=1);

namespace Storyblok\Mapi\Data\Enum;

enum Region: string
{
    case EU = 'EU';
    case US = 'US';
    case AP = 'AP';
    case CA = 'CA';
    case CN = 'CN';

    /**
     * Get all region values as an array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Check if a value is a valid region.
     *
     * @param string $value the code of region to be validated
     * @return bool true if the region is one of the valid region (Upper case)
     */
    public static function isValid(string $value): bool
    {
        return in_array($value, self::values(), true);
    }
}
