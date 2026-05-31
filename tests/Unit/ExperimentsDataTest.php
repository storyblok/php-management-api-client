<?php

declare(strict_types=1);

namespace Tests\Unit;

use Storyblok\ManagementApi\Data\Experiment;
use Storyblok\ManagementApi\Data\Experiments;
use Tests\TestCase;

final class ExperimentsDataTest extends TestCase
{
    public function testExperimentsCollection(): void
    {
        $contentString = $this->mockData("list-experiments");
        /** @var array<string, array<mixed>> $content */
        $content = json_decode($contentString, true);

        $experiments = Experiments::makeFromResponse($content);

        $this->assertSame(2, $experiments->howManyExperiments());
        $this->assertInstanceOf(Experiment::class, $experiments[0]);
        $this->assertSame('Homepage Hero Test', $experiments[0]->displayName());
    }
}
