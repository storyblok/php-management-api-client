<?php

declare(strict_types=1);

arch()
    ->expect('Storyblok\ManagementApi')
    ->toUseStrictTypes()
    ->not->toUse(['die', 'dd', 'var_dump', 'dump', 'echo', 'print_r']);

arch()->preset()->php();
