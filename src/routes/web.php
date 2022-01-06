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

    $router->get('org/user/auth', ['middleware' => 'auth:user orgUserAuth', function() {
        return Response('user resource authorized');
    }]);
}

$router->group(['prefix' => 'user'], function () use ($router) {
    $router->get('', [
        'as' => 'user-show',
        'uses' => 'User\Show@show',
        'middleware' => 'auth:user'
    ]);

    $router->post('login', [
        'as' => 'user-login',
        'uses' => 'User\Login@login'
    ]);

    $router->post('', [
        'as' => 'user-create',
        'uses' => 'User\Create@create',
        'middleware' => 'auth:owner'
    ]);

    $router->put('', [
        'as' => 'user-edit',
        'uses' => 'User\Edit@edit',
        'middleware' => 'auth:user'
    ]);
});
