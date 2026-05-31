<?php

declare(strict_types=1);

namespace Tests\Unit;

use Storyblok\ManagementApi\Data\ExperimentResult;
use Tests\TestCase;

final class ExperimentResultDataTest extends TestCase
{
    private function makeExperimentResult(): ExperimentResult
    {
        $contentString = $this->mockData("one-experiment-result");
        /** @var array<string, array<mixed>> $content */
        $content = json_decode($contentString, true);

        return ExperimentResult::makeFromResponse($content);
    }

    public function testExperimentResultObjectAccessors(): void
    {
        $experimentResult = $this->makeExperimentResult();

        $this->assertSame('123456789', $experimentResult->id());
        $this->assertSame('987654321', $experimentResult->experimentId());
        $this->assertSame('bar', $experimentResult->charts()[0]['kind']);
        $this->assertSame('2026-03-15T10:30:00.000Z', $experimentResult->pushedAt());
        $this->assertSame('2026-03-15T10:30:00.000Z', $experimentResult->createdAt());
        $this->assertSame('2026-03-15T10:30:00.000Z', $experimentResult->updatedAt());
    }

    public function testExperimentResultPayloadBuilder(): void
    {
        $experimentResult = ExperimentResult::forCharts([]);
        $experimentResult->addChart([
            "kind" => "text",
            "body" => "Variant A shows 95% confidence of improvement.",
        ]);

        $this->assertSame([
            "charts" => [
                [
                    "kind" => "text",
                    "body" => "Variant A shows 95% confidence of improvement.",
                ],
            ],
        ], $experimentResult->toArray());
    }
}
