<?php

namespace Tests;

use App;
use App\User;
use Illuminate\Support\Facades\Artisan;

trait ResetsDatabase
{
    protected function setUp(): void
    {
        parent::setUp();

        ray()->showApp();

        ray()->clearAll();
        ray()->className($this)->blue();

        App\Console\Commands\ClearDatabaseCommand::resetDatabase();

        Artisan::call('app:install');

        User::factory()->create([
            'email' => 'demo-admin@ship.town',
            'password' => bcrypt('secret1144'),
        ]);
    }
}
