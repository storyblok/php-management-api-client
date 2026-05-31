<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

class Experiment extends StoryblokData
{
    public function setName(string $name): self
    {
        $this->set('name', $name);
        return $this;
    }

    public function setDisplayName(string $displayName): self
    {
        $this->set('display_name', $displayName);
        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->set('description', $description);
        return $this;
    }

    /**
     * @param array<int, int> $storyIds
     */
    public function setStoryIds(array $storyIds): self
    {
        $this->set('story_ids', $storyIds);
        return $this;
    }

    /**
     * @param array<int, array<string, mixed>> $experimentVariantsAttributes
     */
    public function setExperimentVariantsAttributes(
        array $experimentVariantsAttributes,
    ): self {
        $this->set(
            'experiment_variants_attributes',
            $experimentVariantsAttributes,
        );
        return $this;
    }

    /**
     * @param array<string, mixed> $experimentVariantAttributes
     */
    public function addExperimentVariantAttributes(
        array $experimentVariantAttributes,
    ): self {
        $experimentVariantsAttributes = $this->arrayList(
            'experiment_variants_attributes',
        );
        $experimentVariantsAttributes[] = $experimentVariantAttributes;
        $this->setExperimentVariantsAttributes($experimentVariantsAttributes);
        return $this;
    }

    public function addExperimentVariant(ExperimentVariant $experimentVariant): self
    {
        return $this->addExperimentVariantAttributes(
            $experimentVariant->toArray(),
        );
    }

    /**
     * @param array<int, ExperimentVariant> $experimentVariants
     */
    public function setExperimentVariants(array $experimentVariants): self
    {
        $experimentVariantsAttributes = [];
        foreach ($experimentVariants as $experimentVariant) {
            $experimentVariantsAttributes[] = $experimentVariant->toArray();
        }

        return $this->setExperimentVariantsAttributes(
            $experimentVariantsAttributes,
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function makeFromResponse(array $data = []): self
    {
        $experiment = $data["experiment"] ?? [];
        return new self(is_array($experiment) ? $experiment : []);
    }

    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    public function id(): string
    {
        return $this->getString('id', "");
    }

    public function name(): string
    {
        return $this->getString('name', "");
    }

    public function displayName(): string
    {
        return $this->getString('display_name', "");
    }

    public function description(): string
    {
        return $this->getString('description', "");
    }

    public function status(): string
    {
        return $this->getString('status', "");
    }

    public function startedAt(): string
    {
        return $this->getString('started_at', "");
    }

    public function endedAt(): string|null
    {
        return $this->getStringNullable('ended_at');
    }

    public function createdAt(): string
    {
        return $this->getString('created_at', "");
    }

    public function updatedAt(): string
    {
        return $this->getString('updated_at', "");
    }

    /**
     * @return array<int, mixed>
     */
    public function storyIds(): array
    {
        return $this->getArray('story_ids', []);
    }

    public function winningVariantId(): string|null
    {
        return $this->getStringNullable('winning_variant_id');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function experimentVariants(): array
    {
        return $this->arrayList('experiment_variants');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function experimentAssignedMetrics(): array
    {
        return $this->arrayList('experiment_assigned_metrics');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function arrayList(string $key): array
    {
        $items = [];
        foreach ($this->getArray($key, []) as $item) {
            if (is_array($item)) {
                $items[] = $item;
            }
        }

        return $items;
    }
}
