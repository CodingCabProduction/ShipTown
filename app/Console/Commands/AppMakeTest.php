<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class AppMakeTest extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'app:make-test {name : The name of the class} {--stub=test: The stub file to load}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Unit test class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Test';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return $this->laravel->basePath().'/stubs/' . $this->option('stub') . '.stub';
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name): string
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel->basePath().'/tests'.str_replace('\\', '/', $name).'.php';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace;
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace(): string
    {
        return 'Tests';
    }

    protected function buildClass($name): array|string
    {
        $buildClass = parent::buildClass($name);

        if ($this->hasOption('uri')) {
            $buildClass = str_replace('{{uri}}', $this->option('uri'), $buildClass);
        }

        return $buildClass;
    }
}
