<?php

declare(strict_types=1);

/**
 * Simple license checker for Composer dependencies
 *
 * Usage: php license-checker.php
 */

// Configure allowed licenses
$allowedLicenses = [
    'MIT',
    'BSD-3-Clause',
    'Apache-2.0',
];

// Optional: Configure packages to exclude from checking
$excludedPackages = [
    // For example: 'vendor/package-name'
];

$output = shell_exec('composer licenses -f json');
if (!$output) {
    echo "Failed to retrieve license information.\n";
    exit(1);
}

$licensesData = json_decode($output, true);
if (!isset($licensesData['dependencies']) || !is_array($licensesData['dependencies'])) {
    echo "Invalid license data format.\n";
    exit(1);
}

echo "Checking licenses against allowed list: " . implode(', ', $allowedLicenses) . "\n\n";

$violations = [];
$checkedCount = 0;

foreach ($licensesData['dependencies'] as $package => $info) {
    if (in_array($package, $excludedPackages, true)) {
        echo "⏩ Skipping excluded package: {$package}\n";
        continue;
    }

    $checkedCount++;
    $packageLicenses = $info['license'] ?? [];
    $version = $info['version'] ?? 'unknown';

    $hasAllowedLicense = false;
    foreach ($packageLicenses as $license) {
        if (in_array($license, $allowedLicenses, true)) {
            $hasAllowedLicense = true;
            break;
        }
    }

    if (!$hasAllowedLicense) {
        $violations[] = [
            'package' => $package,
            'version' => $version,
            'licenses' => $packageLicenses,
        ];
        echo "❌ License violation: {$package} ({$version}) uses " . implode(', ', $packageLicenses) . "\n";
    } else {
        echo "✅ {$package} ({$version}) uses " . implode(', ', $packageLicenses) . "\n";
    }
}

echo "\n";
echo "Summary:\n";
echo "- Packages checked: {$checkedCount}\n";
echo "- Violations found: " . count($violations) . "\n";

if (count($violations) > 0) {
    echo "\nLicense violations detected. Please review the dependencies above.\n";
    exit(1);
} else {
    echo "\nAll dependencies comply with the allowed licenses.\n";
    exit(0);
}
