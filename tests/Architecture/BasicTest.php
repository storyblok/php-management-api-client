<?php

declare(strict_types=1);

arch()
    ->expect('Storyblok\Mapi\Endpoints')
    ->toUseStrictTypes()
    ->not->toUse(['die', 'dd', 'dump', 'echo', 'print_r']);

arch()->preset()->php();
