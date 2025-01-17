<?php

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Router;

/** @var Router $router */

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

        $router->get('status/auth', ['middleware' => ['auth:user', 'statAuth'], function () {
            return Response('status resource authorized');
        }]);

        $router->get('unit/auth', ['middleware' => ['auth:user', 'unitAuth'], function () {
            return Response('clothing unit resource authorized');
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

    $router->delete('', [
        'as' => 'org-delete',
        'uses' => 'Org\Delete@delete',
        'middleware' => 'auth:owner'
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

    $router->delete('org', [
        'as' => 'house-org-delete',
        'uses' => 'House\OrgDelete@delete',
        'middleware' => ['auth:owner', 'houseAuth']
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
        'as' => 'cloth-delete',
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

    $router->group(['prefix' => 'unit'], function () use ($router) {
        $router->post('', [
            'as' => 'unit-create',
            'uses' => 'Cloth\Unit\Create@create',
            'middleware' => ['auth:admin', 'clothAuth', 'statAuth', 'houseAuth']
        ]);

        $router->get('', [
            'as' => 'unit-index',
            'uses' => 'Cloth\Unit\Index@index',
            'middleware' => 'auth:user'
        ]);

        $router->get('warehouse', [
            'as' => 'unit-house-index',
            'uses' => 'Cloth\Unit\HouseIndex@index',
            'middleware' => ['auth:user', 'houseAuth']
        ]);

        $router->get('org', [
            'as' => 'unit-org-index',
            'uses' => 'Cloth\Unit\OrgIndex@index',
            'middleware' => 'auth:user'
        ]);

        $router->put('', [
            'as' => 'unit-edit',
            'uses' => 'Cloth\Unit\Edit@edit',
            'middleware' => ['auth:admin', 'unitAuth']
        ]);

        $router->delete('', [
            'as' => 'unit-delete',
            'uses' => 'Cloth\Unit\Delete@delete',
            'middleware' => ['auth:admin', 'unitAuth']
        ]);
    });

    $router->group(['prefix' => 'status'], function () use ($router) {
        $router->post('', [
            'as' => 'stat-create',
            'uses' => 'Cloth\Stat\Create@create',
            'middleware' => 'auth:admin'
        ]);

        $router->get('', [
            'as' => 'stat-index',
            'uses' => 'Cloth\Stat\Index@index',
            'middleware' => 'auth:user'
        ]);

        $router->put('', [
           'as' => 'stat-edit',
           'uses' => 'Cloth\Stat\Edit@edit',
           'middleware' => ['auth:admin', 'statAuth']
        ]);

        $router->delete('', [
            'as' => 'stat-delete',
            'uses' => 'Cloth\Stat\Delete@delete',
            'middleware' => ['auth:admin', 'statAuth']
        ]);
    });
});

// Transaction module
$router->group(['prefix' => 'report'], function () use ($router) {
    $router->post('transaction', [
        'as' => 'trans-create',
        'uses' => 'Trans\Create@create',
        'middleware' => ['auth:admin', 'unitAuth', 'houseAuth']
    ]);

    $router->get('history', [
        'as' => 'trans-index',
        'uses' => 'Trans\Index@index',
        'middleware' => ['auth:admin', 'unitAuth']
    ]);
});
