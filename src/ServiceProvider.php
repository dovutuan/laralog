<?php

namespace Dovutuan\Laralog;

use Dovutuan\Laralog\Traits\LogQuery;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{
    use LogQuery;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/Config/laralog.php';
        $this->mergeConfigFrom($configPath, 'laralog');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $configPath = __DIR__ . '/Config/laralog.php';

        if ($this->app->runningInConsole()) {
            $this->publishes([$configPath => config_path('laralog.php')], 'laralog');
        }

        $this->logQuery();
    }
}