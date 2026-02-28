<?php

declare(strict_types=1);

namespace Tests\Unit;

use Storyblok\ManagementApi\QueryParameters\SpacesParams;
use Tests\TestCase;

final class SpacesParamsTest extends TestCase
{
    public function testToArrayWithSearch(): void
    {
        $params = new SpacesParams(search: 'Example');
        $this->assertSame(['search' => 'Example'], $params->toArray());
    }

    public function testToArrayWithNull(): void
    {
        $params = new SpacesParams();
        $this->assertSame([], $params->toArray());
    }
}
