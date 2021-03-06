<?php

use Stubs\User;

$factory->define(Stubs\User::class, function (Faker\Generator $faker) {

    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->state(Stubs\User::class, 'custom', function (Faker\Generator $faker) {

	return [
		'custom' => 'custom',
	];

});