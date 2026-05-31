<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Enum;

enum ExperimentStatus: string
{
    case Draft = 'draft';
    case Running = 'running';
    case Paused = 'paused';
    case Completed = 'completed';
}
