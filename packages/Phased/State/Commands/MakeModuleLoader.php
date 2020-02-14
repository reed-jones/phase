<?php

declare(strict_types=1);

namespace Phased\State\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeModuleLoader extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:loader
                                {module : Vuex module namespace}';

    /**
     * Hides the command from the php artisan route helper.
     */
    protected function configure(): void
    {
        //
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $module = Str::studly($this->argument('module'));

        file_put_contents(
            app_path("VuexLoaders/{$module}ModuleLoader.php"),
            $this->compileModuleLoaderStub($module)
        );
    }

    protected function compileModuleLoaderStub($module)
    {
        $namespaced = str_replace(
            '{{namespace}}',
            $this->laravel->getNamespace(),
            file_get_contents(__DIR__.'/../stubs/ModuleLoader.stub')
        );

        return str_replace('{{module}}', $module, $namespaced);
    }
}
