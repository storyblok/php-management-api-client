<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Storyblok\ManagementApi\Data\Space;
use Tests\TestCase;

final class SpaceDataTest extends TestCase
{
    #[Test]
    public function constructor_always_includes_name_in_payload(): void
    {
        $space = new Space('My Space');

        $this->assertArrayHasKey('name', $space->toArray());
        $this->assertSame('My Space', $space->toArray()['name']);
        $this->assertSame('My Space', $space->name());
    }

    #[Test]
    public function constructor_with_empty_name_does_not_include_name_in_payload(): void
    {
        $space = new Space('');

        $this->assertArrayNotHasKey('name', $space->toArray());
        $this->assertSame([], $space->toArray());
    }

    #[Test]
    public function constructor_with_no_argument_does_not_include_name_in_payload(): void
    {
        $space = new Space();

        $this->assertArrayNotHasKey('name', $space->toArray());
        $this->assertSame([], $space->toArray());
    }

    #[Test]
    public function for_update_does_not_include_name_by_default(): void
    {
        $space = Space::forUpdate([
            'domain' => 'https://new.example.com',
        ]);

        $this->assertArrayNotHasKey('name', $space->toArray());
        $this->assertSame('', $space->name()); // name() returns '' when key is absent
    }

    #[Test]
    public function for_update_contains_only_the_specified_fields(): void
    {
        $folderIds = [123, 456];
        $folderConfig = [
            ['folder_id' => 123, 'ai_translation_code' => ''],
            ['folder_id' => 456, 'ai_translation_code' => 'it'],
        ];

        $space = Space::forUpdate([
            'dimensions_app_folder_ids' => $folderIds,
            'dimensions_app_folders'    => $folderConfig,
        ]);

        $payload = $space->toArray();

        $this->assertArrayNotHasKey('name', $payload);
        $this->assertArrayHasKey('dimensions_app_folder_ids', $payload);
        $this->assertArrayHasKey('dimensions_app_folders', $payload);
        $this->assertSame($folderIds, $payload['dimensions_app_folder_ids']);
        $this->assertSame($folderConfig, $payload['dimensions_app_folders']);
        $this->assertCount(2, $payload); // exactly the two fields we provided
    }

    #[Test]
    public function for_update_with_empty_array_produces_empty_payload(): void
    {
        $space = Space::forUpdate([]);

        $this->assertSame([], $space->toArray());
        $this->assertArrayNotHasKey('name', $space->toArray());
    }

    #[Test]
    public function for_update_with_single_field(): void
    {
        $space = Space::forUpdate(['domain' => 'https://staging.example.com']);

        $this->assertSame(
            ['domain' => 'https://staging.example.com'],
            $space->toArray(),
        );
        $this->assertSame('https://staging.example.com', $space->domain());
        $this->assertArrayNotHasKey('name', $space->toArray());
    }

    #[Test]
    public function for_update_allows_name_when_explicitly_provided(): void
    {
        $space = Space::forUpdate(['name' => 'Renamed Space', 'domain' => 'https://example.com']);

        $this->assertSame('Renamed Space', $space->name());
        $this->assertArrayHasKey('name', $space->toArray());
    }

    #[Test]
    public function make_hydrates_full_space_from_api_data(): void
    {
        $space = Space::make([
            'name'   => 'My Space',
            'id'     => '42',
            'domain' => 'https://example.com',
        ]);

        $this->assertSame('My Space', $space->name());
        $this->assertSame('42', $space->id());
        $this->assertSame('https://example.com', $space->domain());
        $this->assertArrayHasKey('name', $space->toArray());
    }

    #[Test]
    public function make_with_empty_array_produces_empty_name(): void
    {
        $space = Space::make([]);

        $this->assertSame('', $space->name());
        $this->assertSame([], $space->toArray());
    }
}
