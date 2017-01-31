<?php

namespace Analogue\Factory;

use Analogue\ORM\System\Manager;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory as EloquentFactory;

class Factory extends EloquentFactory {

    protected $manager;

    /**
     * Create a new factory instance.
     *
     * @param  \Faker\Generator  $faker
     * @param  \Analogue\ORM\System\Manager  $manager
     *
     * @return void
     */
    public function __construct(Faker $faker, Manager $manager)
    {
        $this->manager = $manager;

        return parent::__construct($faker);
    }

    /**
     * Create a new factory container.
     *
     * @param  \Faker\Generator  $faker
     * @param  string|null  $pathToFactories
     * @return static
     */
    public static function construct(Faker $faker, $pathToFactories = null, Manager $manager = null)
    {
        $pathToFactories = $pathToFactories ?: database_path('factories');

        return (new static($faker, $manager))->load($pathToFactories);
    }

    /**
     * Create a builder for the given entity.
     *
     * @param  string  $class
     * @param  string  $name
     * @return \Analogue\Factory\FactoryBuilder
     */
    public function of($class, $name = 'default')
    {
        return new FactoryBuilder($this->manager, $class, $name, $this->definitions, $this->states, $this->faker);
    }

}
