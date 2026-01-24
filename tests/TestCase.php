<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

abstract class TestCase extends BaseTestCase
{
    /**
     * @param array<string, int|string> $headers
     */
    protected function mockResponse(
        string $mockfile = 'test',
        int $statusCode = 200,
        array $headers = [],
    ): MockResponse {
        $content = file_get_contents(sprintf('./tests/Feature/Data/%s.json', $mockfile));

        return new MockResponse($content, [
            'http_code' => $statusCode,
            'response_headers' => $headers,
        ]);
    }
}
