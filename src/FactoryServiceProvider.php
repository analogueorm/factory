<?php

namespace Analogue\Factory;

use Illuminate\Support\ServiceProvider;
use Faker\Factory as Faker;

class FactoryServiceProvider extends ServiceProvider
{
     /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot()
    {
        require_once(__DIR__.'/helpers.php');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Factory::class, function ($app) {
            $faker = Faker::create();

            $analogueManager = $app->make('analogue');

            return Factory::construct($faker, database_path('factories'), $analogueManager);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
