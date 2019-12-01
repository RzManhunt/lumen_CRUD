<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/key', function () {
    return App\User::keyGenerator();
});

$router->post('/', ['uses' => 'UserController@getToken']);

$router->group(['middleware' => ['auth']], function() use ($router) {
	$router->get('/users', ['uses' => 'UserController@index']);
	$router->post('/users', ['uses' => 'UserController@create']);
	$router->put('/users', ['uses' => 'UserController@update']);
	$router->patch('/users', ['uses' => 'UserController@update']);
	$router->delete('/users', ['uses' => 'UserController@delete']);
});