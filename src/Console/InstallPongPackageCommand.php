<?php

namespace Ahoiroman\Pong\Console;

use Ahoiroman\Pong\ServiceProviders\PongServiceProvider;
use Illuminate\Console\Command;

class InstallPongPackageCommand extends Command
{
    protected $signature = 'pong:install';

    protected $description = 'Install the pong package';

    public function handle()
    {
        $this->info('Installing Pong...');

        $this->info('Publishing configuration...');

        $this->call('vendor:publish', [
            '--provider' => PongServiceProvider::class,
            '--tag'      => 'config',
        ]);

        $this->info('Installed Pong');
    }
}
