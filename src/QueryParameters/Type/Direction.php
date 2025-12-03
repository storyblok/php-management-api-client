<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\QueryParameters\Type;

enum Direction: string
{
    case Asc = 'asc';

    case Desc = 'desc';
}
