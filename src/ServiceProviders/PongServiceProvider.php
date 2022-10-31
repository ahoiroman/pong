<?php

namespace Ahoiroman\Pong\ServiceProviders;

use Ahoiroman\Pong\Console\InstallPongPackageCommand;
use Ahoiroman\Pong\Ping;
use Illuminate\Support\ServiceProvider;

class PongServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishCommands();
            $this->publishConfiguration();
        }
    }

    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'ping');

        // Register the main class to use with the facade
        $this->app->singleton('ping', function () {
            return new Ping();
        });
    }

    private function publishCommands(): void
    {
        if (!file_exists(config_path('ping.php'))) {
            $this->commands([
                                InstallPongPackageCommand::class,
            ]);
        }
    }

    private function publishConfiguration(): void
    {
        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('ping.php'),
        ], 'config');
    }
}
