<?php

namespace App\Listeners;

use App\Console\Commands\AppInstall;

class SchemaLoadedListener
{
    public function handle($event): void
    {
        AppInstall::ensureAppKeysGenerated();
    }
}
