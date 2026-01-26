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
        string $mockfile = "test",
        int $statusCode = 200,
        array $headers = [],
    ): MockResponse {
        $content = $this->mockData($mockfile);

        return new MockResponse($content, [
            "http_code" => $statusCode,
            "response_headers" => $headers,
        ]);
    }

    protected function mockData(string $mockfile = "test"): string
    {
        $path = sprintf("./tests/Feature/Data/%s.json", $mockfile);
        $content = file_get_contents($path);
        if ($content === false) {
            throw new \RuntimeException(
                sprintf("Failed to read mock file: %s", $path),
            );
        }

        return $content;
    }
}
