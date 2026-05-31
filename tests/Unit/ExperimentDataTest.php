<?php

declare(strict_types=1);

namespace Tests\Unit;

use Storyblok\ManagementApi\Data\Experiment;
use Storyblok\ManagementApi\Data\ExperimentVariant;
use Tests\TestCase;

final class ExperimentDataTest extends TestCase
{
    private function makeExperiment(): Experiment
    {
        $contentString = $this->mockData("one-experiment");
        /** @var array<string, array<mixed>> $content */
        $content = json_decode($contentString, true);

        return Experiment::makeFromResponse($content);
    }

    public function testExperimentObjectAccessors(): void
    {
        $experiment = $this->makeExperiment();

        $this->assertSame('123', $experiment->id());
        $this->assertSame('homepage_hero_test', $experiment->name());
        $this->assertSame('Homepage Hero Test', $experiment->displayName());
        $this->assertSame('draft', $experiment->status());
        $this->assertSame([101, 202], $experiment->storyIds());
        $this->assertNull($experiment->winningVariantId());
        $this->assertSame('control', $experiment->experimentVariants()[0]['name']);
        $metricDefinition = $experiment->experimentAssignedMetrics()[0]['metric_definition'];
        $this->assertIsArray($metricDefinition);
        $this->assertSame('purchase_rate', $metricDefinition['name']);
    }

    public function testExperimentPayloadBuilder(): void
    {
        $experiment = Experiment::make()
            ->setName('a_simple_test')
            ->setDisplayName('A simple test')
            ->setDescription('A short description for a simple test')
            ->setStoryIds([176024833123843])
            ->addExperimentVariantAttributes([
                "display_name" => "Control",
                "is_control" => true,
                "name" => "control",
                "weight" => 60,
            ])
            ->addExperimentVariantAttributes([
                "display_name" => "Test",
                "is_control" => false,
                "name" => "test",
                "weight" => 40,
            ]);

        $this->assertSame([
            "name" => "a_simple_test",
            "display_name" => "A simple test",
            "description" => "A short description for a simple test",
            "story_ids" => [176024833123843],
            "experiment_variants_attributes" => [
                [
                    "display_name" => "Control",
                    "is_control" => true,
                    "name" => "control",
                    "weight" => 60,
                ],
                [
                    "display_name" => "Test",
                    "is_control" => false,
                    "name" => "test",
                    "weight" => 40,
                ],
            ],
        ], $experiment->toArray());
    }

    public function testExperimentPayloadBuilderWithTypedVariants(): void
    {
        $experiment = Experiment::make()
            ->setName('a_simple_test')
            ->setDisplayName('A simple test')
            ->setExperimentVariants([
                ExperimentVariant::make()
                    ->setName('control')
                    ->setDisplayName('Control')
                    ->setWeight(60)
                    ->setControl(true),
                ExperimentVariant::make()
                    ->setName('test')
                    ->setDisplayName('Test')
                    ->setWeight(40)
                    ->setControl(false),
            ]);

        $this->assertSame([
            "name" => "a_simple_test",
            "display_name" => "A simple test",
            "experiment_variants_attributes" => [
                [
                    "name" => "control",
                    "display_name" => "Control",
                    "weight" => 60,
                    "is_control" => true,
                ],
                [
                    "name" => "test",
                    "display_name" => "Test",
                    "weight" => 40,
                    "is_control" => false,
                ],
            ],
        ], $experiment->toArray());
    }
}
