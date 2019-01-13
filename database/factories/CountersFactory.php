<?php

use Faker\Generator as Faker;

$factory->define(App\Counters::class, function (Faker $faker) {
    return [
        'counter_name' => $faker->userName,
        'position' => $faker->numberBetween(1,500)
    ];
});