<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

if (in_array(env('APP_ENV'), ['local', 'testing'])) {
    $router->group(['prefix' => 'user/auth'], function () use($router) {
        $router->get('user', ['middleware' => 'auth:user', function () {
            return Response('user authorization successful');
        }]);
        $router->get('admin', ['middleware' => 'auth:admin', function () {
            return Response('admin authorization successful');
        }]);
        $router->get('owner', ['middleware' => 'auth:owner', function () {
            return Response('owner authorization successful');
        }]);
    });
}

$router->get('/user', [
    'as' => 'user-show',
    'uses' => 'User\Show@show',
    'middleware' => 'auth:user'
]);

$router->post('/user/login', [
    'as' => 'user-login',
    'uses' => 'User\Login@login'
]);

$router->post('/user', [
    'as' => 'user-create',
    'uses' => 'User\Create@create',
    'middleware' => 'auth:owner'
]);

$router->put('/user', [
    'as' => 'user-edit',
    'uses' => 'User\Edit@edit',
    'middleware' => 'auth:user'
]);
