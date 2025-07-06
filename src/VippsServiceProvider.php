<?php

namespace Wirement\Vipps;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Wirement\Vipps\Commands\CreateWebhookCommand;

class VippsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('wirement-vipps')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_vipps_tokens_table')
            ->hasCommand(CreateWebhookCommand::class);
    }

    public function packageBooted()
    {
        $this->app->bind('vipps', function () {
            return new Vipps;
        });
    }
}
