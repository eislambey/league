<?php

namespace App\Providers;

use App\Services\Fixture\Strategy\ResultStrategyInterface;
use App\Services\Fixture\Strategy\TeamPowerStrategy;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->app->singleton(ResultStrategyInterface::class, function (Application $app): ResultStrategyInterface {
            return new TeamPowerStrategy();
        });

        if ($this->app->isProduction()) {
            URL::forceHttps();
        }
    }
}
