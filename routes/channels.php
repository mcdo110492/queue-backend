<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/



Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('ticket-call.{department_id}', function() {

    $token = auth()->user();

    return ($token['role'] === 1 || $token['role'] === 2);

});

Broadcast::channel('ticket-back-to-queue.{department_id}', function() {

    $token = auth()->user();

    return ($token['role'] === 1 || $token['role'] === 2);
});


Broadcast::channel('issue-ticket.{department_id}', function() {

    $token = auth()->user();

    return ($token['role'] === 1 || $token['role'] === 2);
});