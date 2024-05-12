<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 *
 */
class AppGenerateModulesTests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-modules-tests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates tests for all not test covered modules';

    /**
     * Command will not override existing files
     * It will only add new if do not exists.
     *
     * @return int
     */
    public function handle(): int
    {
        $modulesList = File::directories('app/modules');

        $expectedTestsList = collect($modulesList)->map(function ($moduleDirectory) {
            $testPath = Str::replaceArray('app/modules/', ['Unit/Modules/'], $moduleDirectory);
            return $testPath . '/BasicModuleTest';
        });

        $expectedTestsList->each(function ($route) {
            $testName = $route;
            $testFileName = app()->basePath() . '/tests/'. $testName . '.php';

            if (File::exists($testFileName)) {
                return true;
            }

            $command = 'app:make-test ' . $testName . ' --stub=test.module';

            Artisan::call($command);
            $this->info(Artisan::output());
        });

        return 0;
    }
}
