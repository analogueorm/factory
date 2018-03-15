<?php

namespace Analogue\Factory;

use Illuminate\Support\Collection;
use Analogue\ORM\System\Manager;
use Analogue\ORM\System\Wrappers\Factory as EntityFactory;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\FactoryBuilder as EloquentFactoryBuilder;
use InvalidArgumentException;

class FactoryBuilder extends EloquentFactoryBuilder {

    /**
     * Analogue Entity Manager
     *
     * @var \Analogue\ORM\System\Manager
     */
    protected $manager;

    /**
     * Analogue's Entity Factory
     *
     * @var \Analogue\ORM\System\Wrappers\Factory
     */
    protected $entityFactory;

    /**
     * If times() is used users expects a Collection
     */
    protected $expectingCollection = false;

    /**
     * Create an new builder instance.
     *
     * @param  \Analogue\ORM\System\Manager
     * @param  string  $class
     * @param  string  $name
     * @param  array  $definitions
     * @param  \Faker\Generator  $faker
     * @return void
     */
    public function __construct(Manager $manager, $class, $name, array $definitions, array $states, Faker $faker)
    {
        $this->manager = $manager;

        $this->entityFactory = new EntityFactory;

        parent::__construct($class, $name, $definitions, $states, [], [], $faker);
    }

    /**
     * Create a collection of models and persist them to the database.
     *
     * @param  array  $attributes
     * @return mixed
     */
    public function create(array $attributes = [])
    {
        $results = $this->make($attributes);

        $this->getMapper()->store($results);

        return $results;
    }

    /**
     * Create a collection of models.
     *
     * @param  array  $attributes
     * @return mixed
     */
    public function make(array $attributes = [])
    {
        if ($this->amount === null) {
            return $this->makeInstance($attributes);
        } else {
            $results = [];

            for ($i = 0; $i < $this->amount; $i++) {
                $results[] = $this->makeInstance($attributes);
            }
            
            return new Collection($results);
        }
    }

    /**
     * Get the mapper's instance for this entity class
     *
     * @return \Analogue\ORM\System\Mapper
     */
    protected function getMapper()
    {
        return $this->manager->getMapper($this->class);
    }

    /**
     * Make an instance of the model with the given attributes.
     *
     * @param  array  $attributes
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    protected function makeInstance(array $attributes = [])
    {
        if (! isset($this->definitions[$this->class][$this->name])) {
            throw new InvalidArgumentException("Unable to locate factory with name [{$this->name}] [{$this->class}].");
        }

        $entityWrapper = $this->entityFactory->make($this->getMapper()->newInstance());

        $definition = call_user_func(
            $this->definitions[$this->class][$this->name], 
            $this->faker, $attributes);

        $entityWrapper->setEntityAttributes(array_merge($this->applyStates($definition, $attributes), $attributes));

        return $entityWrapper->unwrap();
    }
}
