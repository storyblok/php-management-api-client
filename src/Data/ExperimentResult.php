<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

class ExperimentResult extends StoryblokData
{
    /**
     * @param array<string, mixed> $data
     */
    public static function makeFromResponse(array $data = []): self
    {
        $experimentResult = $data["experiment_result"] ?? [];
        return new self(is_array($experimentResult) ? $experimentResult : []);
    }

    #[\Override]
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    /**
     * @param array<int, array<string, mixed>> $charts
     */
    public static function forCharts(array $charts): self
    {
        return new self(["charts" => $charts]);
    }

    public function id(): string
    {
        return $this->getString('id', "");
    }

    public function experimentId(): string
    {
        return $this->getString('experiment_id', "");
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function charts(): array
    {
        $charts = [];
        foreach ($this->getArray('charts', []) as $chart) {
            if ($this->isStringKeyedArray($chart)) {
                $charts[] = $chart;
            }
        }

        return $charts;
    }

    /**
     * @param array<int, array<string, mixed>> $charts
     */
    public function setCharts(array $charts): self
    {
        $this->set('charts', $charts);
        return $this;
    }

    /**
     * @param array<string, mixed> $chart
     */
    public function addChart(array $chart): self
    {
        $charts = $this->charts();
        $charts[] = $chart;
        $this->setCharts($charts);
        return $this;
    }

    public function pushedAt(): string
    {
        return $this->getString('pushed_at', "");
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
     * @phpstan-assert-if-true array<string, mixed> $value
     */
    private function isStringKeyedArray(mixed $value): bool
    {
        if (! is_array($value)) {
            return false;
        }

        foreach (array_keys($value) as $key) {
            if (! is_string($key)) {
                return false;
            }
        }

        return true;
    }
}
