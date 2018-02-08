# Analogue ORM - Entity Factory

This package adds support the laravel's Model Factory' functionnality to the Analogue datamapper ORM. 

## Installation

Add this line to your composer.json file : 

```
composer require analogue/factory
```

## Configuration

Add the Service Provider to config/app.php :

```
'Analogue\Factory\FactoryServiceProvider',
```

## Usage

Analogue Factory uses the same definitions mechanism Eloquent's does. Out of the box, this file provided with the default Laravel's install contains one factory definition:

    $factory->define(App\User::class, function (Faker\Generator $faker) {
        return [
            'name' => $faker->name,
            'email' => $faker->email,
            'password' => bcrypt(str_random(10)),
            'remember_token' => str_random(10),
        ];
    });

Within the Closure, which serves as the factory definition, you may return the default test values of all attributes on the model. The Closure will receive an instance of the [Faker](https://github.com/fzaninotto/Faker) PHP library, which allows you to conveniently generate various kinds of random data for testing.

Of course, you are free to add your own additional factories to the `ModelFactory.php` file.

### Multiple Factory Types

Sometimes you may wish to have multiple factories for the same Analogue Entity class. For example, perhaps you would like to have a factory for "Administrator" users in addition to normal users. You may define these factories using the `defineAs` method:

    $factory->defineAs(App\User::class, 'admin', function ($faker) {
        return [
            'name' => $faker->name,
            'email' => $faker->email,
            'password' => str_random(10),
            'remember_token' => str_random(10),
            'admin' => true,
        ];
    });

Instead of duplicating all of the attributes from your base user factory, you may use the `raw` method to retrieve the base attributes. Once you have the attributes, simply supplement them with any additional values you require:

    $factory->defineAs(App\User::class, 'admin', function ($faker) use ($factory) {
        $user = $factory->raw(App\User::class);

        return array_merge($user, ['admin' => true]);
    });

### Using Factories In Tests

Once you have defined your factories, you may use them in your tests or database seed files to generate model instances using the global `analogue_factory` function. So, let's take a look at a few examples of creating models. First, we'll use the `make` method, which creates models but does not save them to the database:

    public function testDatabase()
    {
        $user = analogue_factory(App\User::class)->make();

        // Use entity in tests...
    }

If you would like to override some of the default values of your entities, you may pass an array of values to the `make` method. Only the specified values will be replaced while the rest of the values remain set to their default values as specified by the factory:

    $user = analogue_factory(App\User::class)->make([
        'name' => 'Abigail',
       ]);

You may also create a Collection of many models or create models of a given type:

    // Create three App\User instances...
    $users = analogue_factory(App\User::class, 3)->make();

    // Create an App\User "admin" instance...
    $user = analogue_factory(App\User::class, 'admin')->make();

    // Create three App\User "admin" instances...
    $users = analogue_factory(App\User::class, 'admin', 3)->make();

### Persisting Factory Entities

The `create` method not only creates the model instances, but also saves them to the database using Analogue's Mapper store() method.

    public function testDatabase()
    {
        $user = analogue_factory(App\User::class)->create();

        // Use entity in tests...
    }

Again, you may override attributes on the model by passing an array to the `create` method:

    $user = analogue_factory(App\User::class)->create([
        'name' => 'Abigail',
       ]);

### Building complex Entities

By recursively calling the `analogue_factory` function, you can generate complex Entities very easily :

    $factory->define(App\Post::class, function (Faker\Generator $faker) {
        return [
            'title' => $faker->sentence,
            'content' => $faker->text,
        ];
    });

    $factory->define(App\User::class, function (Faker\Generator $faker) {
        return [
            'name' => $faker->name,
            'posts' => analogue_factory(App\Post::class, 10),
        ];
    });


You can even persist these complex entities with a single call :

    $users = analogue_factory(App\User::class, 3)->create();


               
