<?php

use Laravel\BrowserKitTesting\Concerns\InteractsWithDatabase;
use Laravel\BrowserKitTesting\Concerns\InteractsWithConsole;
use Illuminate\Contracts\Http\Kernel;
use Analogue\Factory\Factory;
use Analogue\ORM\AnalogueServiceProvider;
use Faker\Factory as Faker;
use Laravel\BrowserKitTesting\TestCase as BaseTestCase;

abstract class FactoryTestCase extends BaseTestCase
{
    use InteractsWithDatabase;

    protected $analogue;

    public function setUp()
    {
        parent::setUp();

        $this->app['config']->set('database.default', 'sqlite');
        $this->app['config']->set('database.connections.sqlite.database', ':memory:');

        $this->app->singleton(Factory::class, function ($app) {
            $faker = Faker::create();;
            $analogueManager = $app->make('analogue');
            return Factory::construct($faker, __DIR__.'/factories', $analogueManager);
        });

        $this->analogue = $this->app->make('analogue');
        $this->analogue->setStrictMode(true);

        $this->migrateDatabase();
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../vendor/laravel/laravel/bootstrap/app.php';

        $app->register(\Analogue\ORM\AnalogueServiceProvider::class);
        $app->register(\Analogue\Factory\FactoryServiceProvider::class);
        
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
   
    /**
     * Run Migrations
     * 
     * @return void
     */
    protected function migrateDatabase()
    {
        $this->artisan('migrate');
    }

}   


