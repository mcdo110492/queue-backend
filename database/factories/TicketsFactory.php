<?php

use Faker\Generator as Faker;
use Carbon\Carbon;



$factory->define(App\Tickets::class, function (Faker $faker) {
    $now = Carbon::now()->toDateString();
    return [
        'name' => $faker->name,
        'ticket_number' => $faker->unique()->randomDigit,
        'priority' => $faker->numberBetween(0,1),
        'status' => 0,
        'date_issued' => $now
    ];
});