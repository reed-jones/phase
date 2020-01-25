<?php

namespace Phased\Preset;

use Illuminate\Support\ServiceProvider;
use Laravel\Ui\UiCommand;
use Phased\Preset\PhasedPreset;

class PhasedPresetServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        UiCommand::macro('phase', function (UiCommand $command) {
            PhasedPreset::install($command);

            $command->info('Preset Complete! Build something amazing!');
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
