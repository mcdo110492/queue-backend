<?php

use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(App\TicketsUsers::class, function (Faker $faker) {
    $now = Carbon::now()->toDateString();
    return [
        'status' => 0,
        'complete_time' => $now,
        'ticket_id' => function() {
            return factory(App\Tickets::class)->create()->id;
        },
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        }
    ];
});