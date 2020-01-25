<?php

namespace Phased\Preset;

use Illuminate\Support\Facades\File;
use Laravel\Ui\Presets\Preset as LaravelPreset;
use Laravel\Ui\UiCommand;

class PhasedPreset extends LaravelPreset
{
    const STUBS = __DIR__.'/stubs/';

    protected static $command = false;

    protected static $packages = [
        'vue' => '^2.6.11',
        'vuex'  => '^3.1.2',
        'vue-router' => '^3.1.3',
        '@phased/phase' => '^0.0.1',
        'vue-template-compiler' => '^2.6.10',
        'tailwindcss' => '^1.1.4',
    ];

    protected static function hasAuth()
    {
        return static::$command->option('auth');
    }

    protected static function options()
    {
        //demo options. todo/counter etc
        return static::$command->option('option');
    }

    public static function install(UiCommand $command)
    {
        static::$command = $command;

        static::cleanDirectories();
        static::updatePackages();
        static::updateScripts();
        static::updateStyles();
        static::updateConfigs();
        static::updatePhaseRoutes();
        static::updatePhaseController();

        if (static::hasAuth()) {
            static::updateAuthControllers();
            static::updateAuthMigrations();
        }
    }

    protected static function cleanDirectories()
    {
        // remove all the things
        File::cleanDirectory(base_path('resources/js'));
        File::cleanDirectory(base_path('resources/sass'));

        // Don't need this anymore
        File::deleteDirectory(base_path('resources/views'));
    }

    protected static function updatePackageArray($packages)
    {
        return array_merge(self::$packages, $packages);
    }

    protected static function updateScripts()
    {
        File::copyDirectory(self::STUBS.'js', base_path('resources/js'));
    }

    protected static function updateStyles()
    {
        File::copyDirectory(self::STUBS.'sass', base_path('resources/sass'));
    }

    protected static function updateConfigs()
    {
        File::copy(self::STUBS.'configs/webpack.mix.js', base_path('webpack.mix.js'));
        File::copy(self::STUBS.'configs/tailwind.config.js', base_path('tailwind.config.js'));
    }

    protected static function updatePhaseRoutes()
    {
        $routes = static::hasAuth() ? 'routes/web.auth.php' : 'routes/web.php';
        File::copy(self::STUBS.$routes, base_path('routes/web.php'));
    }

    protected static function updatePhaseController()
    {
        File::copy(self::STUBS.'controllers/PhaseController.php', base_path('app/Http/Controllers/PhaseController.php'));
    }

    protected static function updateAuthControllers()
    {
        static::$command->callSilent('ui:controllers');
    }

    protected static function updateAuthMigrations()
    {
        copy(
            base_path('vendor/laravel/ui/stubs/migrations/2014_10_12_100000_create_password_resets_table.php'),
            base_path('database/migrations/2014_10_12_100000_create_password_resets_table.php')
        );
    }
}
