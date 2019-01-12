<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('login', 'AuthenticationJWT@login');
    Route::post('logout', 'AuthenticationJWT@logout');
    Route::post('refresh', 'AuthenticationJWT@refresh');
    Route::post('me', 'AuthenticationJWT@me');
});


Route::post('/users', 'UsersController@create');

Route::post('/users/reset/password', 'UsersController@resetPassword');



Route::get('/announcements', 'AnnouncementsController@getWithPagination');

Route::post('/announcements', 'AnnouncementsController@store');

Route::put('/announcements/{id}', 'AnnouncementsController@update');


Route::get('/counters', 'CountersController@getWithPagination');

Route::post('/counters', 'CountersController@store');

Route::put('/counters/{id}', 'CountersController@update');


Route::get('/counters/users', 'UsersCountersController@getAll');

Route::post('/counters/users', 'UsersCountersController@store');

Route::put('/counters/users/{id}', 'UsersCountersController@update');

// Handle Tickets Queue Routes

Route::get('/tickets/pending', 'TicketsController@getNowPending');

Route::post('/tickets/generate', 'TicketsController@issue');

Route::post('/tickets/call', 'TicketsController@call');

Route::post('/tickets/serving', 'TicketsController@serving');

Route::post('/tickets/complete', 'TicketsController@complete');

Route::post('/tickets/backToQueue', 'TicketsController@backToQueue');

Route::post('/tickets/stop', 'TicketsController@stop');