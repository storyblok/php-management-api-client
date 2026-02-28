<?php

declare(strict_types=1);

namespace Tests\Unit;

use Storyblok\ManagementApi\QueryParameters\AppsParams;
use Tests\TestCase;

final class AppsParamsTest extends TestCase
{
    public function testToArrayWithDefaults(): void
    {
        $params = new AppsParams(spaceId: 12345);
        $this->assertSame([
            'space_id' => 12345,
            'page' => 1,
            'per_page' => 25,
        ], $params->toArray());
    }

    public function testToArrayWithCustomValues(): void
    {
        $params = new AppsParams(spaceId: '99999', page: 3, perPage: 10);
        $this->assertSame([
            'space_id' => '99999',
            'page' => 3,
            'per_page' => 10,
        ], $params->toArray());
    }
}
