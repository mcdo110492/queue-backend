<?php


Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'AuthenticationJWT@login');
    Route::post('logout', 'AuthenticationJWT@logout');
    Route::post('refresh', 'AuthenticationJWT@refresh');
    Route::post('me', 'AuthenticationJWT@me');
    Route::post('client/route/guard', 'AuthenticationJWT@clientRouteGuard');
});


Route::get('/users', 'UsersController@getUsers');

Route::get('/users/all/{department_id}', 'UsersController@getAll');

Route::post('/users', 'UsersController@create');

Route::put('/users/{id}', 'UsersController@update');

Route::post('/users/validate', 'UsersController@checkUniqueValue');

Route::post('/users/reset/password', 'UsersController@resetPassword');



Route::get('/announcements', 'AnnouncementsController@getAll');

Route::get('/announcements/visible', 'AnnouncementsController@getAllVisible');

Route::post('/announcements', 'AnnouncementsController@store');

Route::put('/announcements/{id}', 'AnnouncementsController@update');

Route::post('/announcements/validate', 'AnnouncementsController@checkUniqueValue');


Route::get('/departments', 'DepartmentsController@getAll');

Route::post('/departments/validate', 'DepartmentsController@checkUniqueValue');

Route::post('/departments', 'DepartmentsController@store');

Route::put('/departments/{id}', 'DepartmentsController@update');


Route::get('/counters', 'CountersController@getAll');

Route::get('/counters/{department_id}', 'CountersController@getByDepartment');

Route::post('/counters/validate', 'CountersController@checkUniqueValue');

Route::post('/counters', 'CountersController@store');

Route::put('/counters/{id}', 'CountersController@update');


Route::get('/counters/users/all', 'UsersCountersController@getAll');

Route::post('/counters/users/validate', 'UsersCountersController@checkUniqueValue');

Route::post('/counters/users', 'UsersCountersController@store');

Route::put('/counters/users/{id}', 'UsersCountersController@update');

// Handle Tickets Queue Routes

Route::get('/tickets/pending', 'TicketsController@getNowPending');

Route::post('/tickets/generate', 'TicketsController@generate');

Route::post('/tickets/call', 'TicketsController@call');

Route::post('tickets/call/again', 'TicketsController@callAgain');

Route::post('/tickets/serving', 'TicketsController@serving');

Route::post('/tickets/complete', 'TicketsController@complete');

Route::post('/tickets/backToQueue', 'TicketsController@backToQueue');

Route::post('/tickets/stop', 'TicketsController@stop');

Route::get('/tickets/last/user/transaction', 'TicketsController@getUserLastTransaction');

Route::get('/tickets/user/logs', 'TicketsController@getUserCurrentLogs');


Route::get('/media/files', 'MediaController@getMedia');

Route::get('/media/all', 'MediaController@getAll');

Route::post('/media/files', 'MediaController@uploadMedia');

Route::put('/media/{id}', 'MediaController@updateMeta');

Route::delete('/media/{id}', 'MediaController@removeFile');