<?php

declare(strict_types=1);

namespace Tests\Unit;

use Storyblok\ManagementApi\Data\ExperimentVariant;
use Tests\TestCase;

final class ExperimentVariantDataTest extends TestCase
{
    public function testExperimentVariantPayloadBuilder(): void
    {
        $variant = ExperimentVariant::make()
            ->setName('control')
            ->setDisplayName('Control')
            ->setWeight(60)
            ->setControl(true);

        $this->assertSame([
            "name" => "control",
            "display_name" => "Control",
            "weight" => 60,
            "is_control" => true,
        ], $variant->toArray());

        $this->assertSame('control', $variant->name());
        $this->assertSame('Control', $variant->displayName());
        $this->assertSame(60, $variant->weight());
        $this->assertTrue($variant->isControl());
    }
}
