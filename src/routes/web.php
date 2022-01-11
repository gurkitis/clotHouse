<?php

use Illuminate\Http\Request;
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

// Testing middleware routes
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

    $router->get('org/user/auth', ['middleware' => ['auth:user', 'orgUserAuth'], function() {
        return Response('user resource authorized');
    }]);

    $router->get('warehouse/auth', ['middleware' => ['auth:user', 'houseAuth'], function (Request $request) {
        return Response($request->attributes->get('house_type'));
    }]);

    $router->group(['prefix' => 'clothing'], function () use ($router) {
        $router->get('category/auth', ['middleware' => ['auth:user', 'catAuth'], function () {
            return Response('category resource authorized');
        }]);

        $router->get('auth', ['middleware' => ['auth:user', 'clothAuth'], function () {
            return Response('clothing resource authorized');
        }]);
    });
}

// User module routes
$router->group(['prefix' => 'user'], function () use ($router) {
    $router->get('', [
        'as' => 'user-show',
        'uses' => 'User\Show@show',
        'middleware' => ['auth:user', 'orgUserAuth']
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

// Organization module routes
$router->group(['prefix' => 'org'], function () use ($router) {
    $router->post('', [
        'as' => 'org-create',
        'uses' => 'Org\Create@create'
    ]);

    $router->get('', [
        'as' => 'org-show',
        'uses' => 'Org\Show@show',
        'middleware' => 'auth:user'
    ]);

    $router->put('', [
        'as' => 'org-edit',
        'uses' => 'Org\Edit@edit',
        'middleware' => 'auth:owner'
    ]);

    $router->post('admin', [
        'as' => 'org-setAdmin',
        'uses' => 'Org\SetAdmin@setAdmin',
        'middleware' => ['auth:owner', 'orgUserAuth']
    ]);

    $router->get('users', [
        'as' => 'org-user-index',
        'uses' => 'Org\UserIndex@userIndex',
        'middleware' => 'auth:user'
    ]);

    $router->delete('admin', [
        'as' => 'org-removeAdmin',
        'uses' => 'Org\RemoveAdmin@removeAdmin',
        'middleware' => ['auth:owner', 'orgUserAuth']
    ]);
});

// Warehouse module routes
$router->group(['prefix' => 'warehouse'], function () use ($router) {
    $router->post('org', [
        'as' => 'house-org-create',
        'uses' => 'House\OrgCreate@create',
        'middleware' => 'auth:owner'
    ]);

    $router->get('', [
        'as' => 'house-show',
        'uses' => 'House\Show@show',
        'middleware' => ['auth:user', 'houseAuth']
    ]);

    $router->put('', [
        'as' => 'house-edit',
        'uses' => 'House\Edit@edit',
        'middleware' => ['auth:user', 'houseAuth']
    ]);

    $router->get('org', [
        'as' => 'house-org-index',
        'uses' => 'House\OrgIndex@index',
        'middleware' => 'auth:user'
    ]);
});

// Clothing module routes
$router->group(['prefix' => 'clothing'], function () use ($router) {
    $router->post('', [
        'as' => 'cloth-create',
        'uses' => 'Cloth\Create@create',
        'middleware' => ['auth:admin', 'catAuth']
    ]);

    $router->get('', [
        'as' => 'cloth-index',
        'uses' => 'Cloth\Index@index',
        'middleware' => 'auth:user'
    ]);

    $router->put('', [
        'as' => 'cloth-edit',
        'uses' => 'Cloth\Edit@edit',
        'middleware' => ['auth:admin', 'clothAuth']
    ]);

    $router->delete('', [
        'as' => 'cloth-edit',
        'uses' => 'Cloth\Delete@delete',
        'middleware' => ['auth:admin', 'clothAuth']
    ]);

    $router->group(['prefix' => 'category'], function () use ($router) {
        $router->post('', [
            'as' => 'cat-create',
            'uses' => 'Cloth\Cat\Create@create',
            'middleware' => 'auth:admin'
        ]);

        $router->get('', [
            'as' => 'cat-index',
            'uses' => 'Cloth\Cat\Index@index',
            'middleware' => 'auth:user'
        ]);

        $router->put('', [
            'as' => 'cat-edit',
            'uses' => 'Cloth\Cat\Edit@edit',
            'middleware' => ['auth:admin', 'catAuth']
        ]);

        $router->delete('', [
            'as' => 'cat-delete',
            'uses' => 'Cloth\Cat\Delete@delete',
            'middleware' => ['auth:admin', 'catAuth']
        ]);
    });

});
