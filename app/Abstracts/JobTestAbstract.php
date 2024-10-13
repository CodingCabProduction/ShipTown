<?php

namespace App\Abstracts;

use Illuminate\Support\Str;
use Tests\TestCase;

abstract class JobTestAbstract extends TestCase
{
    public static function createJob(): mixed
    {
        // Example
        // calledCass = Tests\Jobs\DispatchEveryMinuteEventJobTest
        // jobName = App\Jobs\DispatchEveryMinuteEventJob
        $jobName = Str::of(get_called_class())
            ->chopEnd('Test')
            ->replaceFirst('Tests\\', 'App\\')
            ->toString();

        return app($jobName);
    }
}
