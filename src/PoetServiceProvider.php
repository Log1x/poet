<?php

namespace Log1x\Poet;

use Illuminate\Support\ServiceProvider;

class PoetServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Log1x\Poet', function () {
            return new Poet($this->app);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/poet.php' => $this->app->configPath('poet.php'),
        ], 'config');

        $this->app->make('Log1x\Poet');
    }
}
