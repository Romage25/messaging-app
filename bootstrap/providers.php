<?php

use App\Providers\AppServiceProvider;
use App\Providers\BroadcastServiceProvider;
use App\Providers\FortifyServiceProvider;

return [
    AppServiceProvider::class,
    FortifyServiceProvider::class,
    BroadcastServiceProvider::class,
];
