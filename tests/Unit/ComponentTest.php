<?php

declare(strict_types=1);

namespace Tests\Unit;

use Storyblok\ManagementApi\Data\Component;
use Tests\TestCase;

final class ComponentTest extends TestCase
{
    public function testIsContentType(): void
    {
        $component = Component::make([
            'name' => 'page',
            'is_root' => true,
            'is_nestable' => false,
        ]);

        $this->assertTrue($component->isContentType());
        $this->assertFalse($component->isNestable());
        $this->assertFalse($component->isUniversal());
    }

    public function testIsNestable(): void
    {
        $component = Component::make([
            'name' => 'feature',
            'is_root' => false,
            'is_nestable' => true,
        ]);

        $this->assertTrue($component->isNestable());
        $this->assertFalse($component->isContentType());
        $this->assertFalse($component->isUniversal());
    }

    public function testIsUniversal(): void
    {
        $component = Component::make([
            'name' => 'universal-block',
            'is_root' => true,
            'is_nestable' => true,
        ]);

        $this->assertTrue($component->isUniversal());
        $this->assertTrue($component->isRoot());
        $this->assertTrue($component->isNestable());
        $this->assertFalse($component->isContentType());
    }

    public function testNeitherRootNorNestable(): void
    {
        $component = Component::make([
            'name' => 'plain',
            'is_root' => false,
            'is_nestable' => false,
        ]);

        $this->assertFalse($component->isContentType());
        $this->assertFalse($component->isNestable());
        $this->assertFalse($component->isUniversal());
    }

    public function testGetComponentTypeDetailContentType(): void
    {
        $component = Component::make([
            'name' => 'page',
            'is_root' => true,
            'is_nestable' => false,
        ]);

        $this->assertSame('content-type', $component->getComponentTypeDetail());
    }

    public function testGetComponentTypeDetailNestable(): void
    {
        $component = Component::make([
            'name' => 'feature',
            'is_root' => false,
            'is_nestable' => true,
        ]);

        $this->assertSame('nestable', $component->getComponentTypeDetail());
    }

    public function testGetComponentTypeDetailUniversal(): void
    {
        $component = Component::make([
            'name' => 'universal-block',
            'is_root' => true,
            'is_nestable' => true,
        ]);

        $this->assertSame('universal', $component->getComponentTypeDetail());
    }

    public function testGetComponentTypeDetailEmpty(): void
    {
        $component = Component::make([
            'name' => 'plain',
            'is_root' => false,
            'is_nestable' => false,
        ]);

        $this->assertSame('', $component->getComponentTypeDetail());
    }

    public function testSetNestable(): void
    {
        $component = Component::make([
            'name' => 'block',
            'is_root' => false,
            'is_nestable' => false,
        ]);

        $this->assertFalse($component->isNestable());

        $component->setNestable(true);
        $this->assertTrue($component->isNestable());
    }
}
