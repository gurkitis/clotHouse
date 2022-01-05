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

if (env('APP_ENV') === 'local') {
    $router->group(['prefix' => 'user/auth'], function () use($router) {
        $router->get('user', ['middleware' => 'admin:user', function () {
            return Response('user authorization successful');
        }]);
        $router->get('admin', ['middleware' => 'admin:admin', function () {
            return Response('admin authorization successful');
        }]);
        $router->get('owner', ['middleware' => 'admin:owner', function () {
            return Response('owner authorization successful');
        }]);
    });
}

$router->get('/user[/{id}]', [
    'as' => 'user-show',
    'uses' => 'User\Show@show'
]);

$router->post('/user/login', [
    'as' => 'user-login',
    'uses' => 'User\Login@login'
]);
