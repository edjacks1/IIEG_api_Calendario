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

$router->get('/', function () use ($router) {
    return $router->app->version();
});




$router->group(['prefix' => 'api/v1/'], function () use ($router) {
    // rutas de recursos de acceso
    $router->post('auth/login', ['as' => 'login', 'uses' => 'Auth\LoginController@authenticate']);
    $router->post('auth/validateToken', ['as' => 'validateToken', 'uses' => 'Auth\LoginController@validateToken']);
    $router->post('auth/refreshToken', ['as' => 'login', 'uses' => 'Auth\LoginController@refreshToken']);
    $router->post('auth/logout', ['as' => 'login', 'uses' => 'Auth\LoginController@logout']);
});

$router->group(
    ['prefix' => 'api/v1', 'middleware' => 'jwt.auth'],
    function () use ($router) {

        //Users Routes
        $router->get('/user', ['middleware' => 'req.permission:see_user', 'as' => 'user.index', 'uses' => 'Admin\UserController@index']);
        $router->get('/user/{id}', ['middleware' => 'req.permission:see_user', 'as' => 'user.show', 'uses' => 'Admin\UserController@show']);
        $router->post('/user', ['middleware' => 'req.permission:create_user', 'as' => 'users.store', 'uses' => 'Admin\UserController@store']);
        $router->put('/user/{id}', ['middleware' => 'req.permission:edit_user', 'as' => 'user.update', 'uses' => 'Admin\UserController@update']);
        $router->delete('/user/{id}', ['middleware' => 'req.permission:delete_user', 'as' => 'user.delete', 'uses' => 'Admin\UserController@destroy']);
        $router->put('/user/assignRole/{id}', ['middleware' => 'req.permission:edit_user', 'as' => 'user.assingrole', 'uses' => 'Admin\UserController@assignRole']);
        $router->get('/user/getRole/{id}', ['middleware' => 'req.permission:see_user', 'as' => 'user.getrole', 'uses' => 'Admin\UserController@getRole']);
        $router->post('/user/checkEmail', ['middleware' => 'req.permission:create_user', 'as' => 'user.checkEmail', 'uses' => 'Admin\UserController@checkEmail']);

        //Role Routes
        $router->get('/role', ['middleware' => 'req.permission:see_role', 'as' => 'role.index', 'uses' => 'Admin\RoleController@index']);
        $router->get('/role/{id}', ['middleware' => 'req.permission:see_role', 'as' => 'role.show', 'uses' => 'Admin\RoleController@show']);
        $router->post('/role', ['middleware' => 'req.permission:create_role', 'as' => 'role.store', 'uses' => 'Admin\RoleController@store']);
        $router->put('/role/{id}', ['middleware' => 'req.permission:edit_role', 'as' => 'role.update', 'uses' => 'Admin\RoleController@update']);
        $router->delete('/role/{id}', ['middleware' => 'req.permission:delete_role', 'as' => 'role.delete', 'uses' => 'Admin\RoleController@destroy']);
        $router->put('/role/assignPermission/{id}', ['middleware' => 'req.permission:edit_role', 'as' => 'role.assingpermissions', 'uses' => 'Admin\RoleController@assignPermission']);
        $router->get('/role/getPermission/{id}', ['middleware' => 'req.permission:see_update', 'as' => 'role.getpermissions', 'uses' => 'Admin\RoleController@getPermission']);

        //Permission Routes
        $router->get('/permission', ['middleware' => 'req.permission:see_permission', 'as' => 'permission.index', 'uses' => 'Admin\PermissionController@index']);
        $router->get('/permission/{id}', ['middleware' => 'req.permission:see_permission', 'as' => 'permission.show', 'uses' => 'Admin\PermissionController@show']);
        // $router->post('/permission', ['middleware' => 'req.permission:create_permission', 'as' => 'permission.store', 'uses' => 'Admin\PermissionController@store']);
        // $router->put('/permission/{id}', ['middleware' => 'req.permission:edit_permission', 'as' => 'permission.update', 'uses' => 'Admin\PermissionController@update']);
        // $router->delete('/permission/{id}', ['middleware' => 'req.permission:delete_permission', 'as' => 'permission.delete', 'uses' => 'Admin\PermissionController@destroy']);

        //Organization Routes
        $router->get('/organization', ['middleware' => 'req.permission:see_organization', 'as' => 'organization.index', 'uses' => 'Catalogs\OrganizationController@index']);
        $router->get('/organization/{id}', ['middleware' => 'req.permission:see_organization', 'as' => 'organization.show', 'uses' => 'Catalogs\OrganizationController@show']);
        $router->post('/organization', ['middleware' => 'req.permission:create_organization', 'as' => 'organization.store', 'uses' => 'Catalogs\OrganizationController@store']);
        $router->put('/organization/{id}', ['middleware' => 'req.permission:edit_organization', 'as' => 'organization.update', 'uses' => 'Catalogs\OrganizationController@update']);
        $router->delete('/organization/{id}', ['middleware' => 'req.permission:delete_organization', 'as' => 'organization.delete', 'uses' => 'Catalogs\OrganizationController@destroy']);
        $router->put('/organization/assignPlace/{id}', ['middleware' => 'req.permission:edit_organization', 'as' => 'role.assingplaces', 'uses' => 'Catalogs\OrganizationController@assignPlace']);

        //Place Routes
        $router->get('/place', ['middleware' => 'req.permission:see_place', 'as' => 'place.index', 'uses' => 'Catalogs\PlaceController@index']);
        $router->get('/place/{id}', ['middleware' => 'req.permission:see_place', 'as' => 'place.show', 'uses' => 'Catalogs\PlaceController@show']);
        $router->post('/place', ['middleware' => 'req.permission:create_place', 'as' => 'place.store', 'uses' => 'Catalogs\PlaceController@store']);
        $router->put('/place/{id}', ['middleware' => 'req.permission:edit_place', 'as' => 'place.update', 'uses' => 'Catalogs\PlaceController@update']);
        $router->delete('/place/{id}', ['middleware' => 'req.permission:delete_place', 'as' => 'place.delete', 'uses' => 'Catalogs\PlaceController@destroy']);

        //Resource Routes
        $router->get('/resource', ['middleware' => 'req.permission:see_resource', 'as' => 'resource.index', 'uses' => 'Catalogs\ResourceController@index']);
        $router->get('/resource/{id}', ['middleware' => 'req.permission:see_resource', 'as' => 'resource.show', 'uses' => 'Catalogs\ResourceController@show']);
        $router->post('/resource', ['middleware' => 'req.permission:create_resource', 'as' => 'resource.store', 'uses' => 'Catalogs\ResourceController@store']);
        $router->put('/resource/{id}', ['middleware' => 'req.permission:edit_resource', 'as' => 'resource.update', 'uses' => 'Catalogs\ResourceController@update']);
        $router->delete('/resource/{id}', ['middleware' => 'req.permission:delete_resource', 'as' => 'resource.delete', 'uses' => 'Catalogs\ResourceController@destroy']);

        //Resource Type Routes
        $router->get('/resourceType', ['middleware' => 'req.permission:see_resourceType', 'as' => 'resourceType.index', 'uses' => 'Catalogs\ResourceTypeController@index']);
        $router->get('/resourceType/{id}', ['middleware' => 'req.permission:see_resourceType', 'as' => 'resourceType.show', 'uses' => 'Catalogs\ResourceTypeController@show']);
        $router->post('/resourceType', ['middleware' => 'req.permission:create_resourceType', 'as' => 'resourceType.store', 'uses' => 'Catalogs\ResourceTypeController@store']);
        $router->put('/resourceType/{id}', ['middleware' => 'req.permission:edit_resourceType', 'as' => 'resourceType.update', 'uses' => 'Catalogs\ResourceTypeController@update']);
        $router->delete('/resourceType/{id}', ['middleware' => 'req.permission:delete_resourceType', 'as' => 'resourceType.delete', 'uses' => 'Catalogs\ResourceTypeController@destroy']);


        //Event Routes
        //Resource Routes
        $router->post('/event/getEvents', ['middleware' => 'req.permission:see_event', 'as' => 'event.index', 'uses' => 'Operation\EventController@index']);
        $router->get('/event/{id}', ['middleware' => 'req.permission:see_event', 'as' => 'event.show', 'uses' => 'Operation\EventController@show']);
        $router->post('/event', ['middleware' => 'req.permission:create_event', 'as' => 'event.store', 'uses' => 'Operation\EventController@store']);
        $router->put('/event/{id}', ['middleware' => 'req.permission:edit_event', 'as' => 'event.update', 'uses' => 'Operation\EventController@update']);
        $router->delete('/event/{id}', ['middleware' => 'req.permission:delete_event', 'as' => 'event.delete', 'uses' => 'Operation\EventController@destroy']);
        $router->get('/event/getResources/{id}', ['middleware' => 'req.permission:see_event', 'as' => 'event.showresources', 'uses' => 'Operation\EventController@getResources']);
        $router->put('/event/checkResources/{id}', ['middleware' => 'req.permission:edit_event', 'as' => 'role.checkresources', 'uses' => 'Operation\EventController@checkResources']);

        //Event Type Routes
        $router->get('/eventType', ['middleware' => 'req.permission:see_eventType', 'as' => 'eventType.index', 'uses' => 'Catalogs\EventTypeController@index']);
        $router->get('/eventType/{id}', ['middleware' => 'req.permission:see_eventType', 'as' => 'eventType.show', 'uses' => 'Catalogs\EventTypeController@show']);
        $router->post('/eventType', ['middleware' => 'req.permission:create_eventType', 'as' => 'eventType.store', 'uses' => 'Catalogs\EventTypeController@store']);
        $router->put('/eventType/{id}', ['middleware' => 'req.permission:edit_eventType', 'as' => 'eventType.update', 'uses' => 'Catalogs\EventTypeController@update']);
        $router->delete('/eventType/{id}', ['middleware' => 'req.permission:delete_eventType', 'as' => 'eventType.delete', 'uses' => 'Catalogs\EventTypeController@destroy']);

        //Event Tag Routes
        $router->get('/eventTag', ['middleware' => 'req.permission:see_eventTag', 'as' => 'eventTag.index', 'uses' => 'Catalogs\EventTagController@index']);
        $router->get('/eventTag/{id}', ['middleware' => 'req.permission:see_eventTag', 'as' => 'eventTag.show', 'uses' => 'Catalogs\EventTagController@show']);
        $router->post('/eventTag', ['middleware' => 'req.permission:create_eventTag', 'as' => 'eventTag.store', 'uses' => 'Catalogs\EventTagController@store']);
        $router->put('/eventTag/{id}', ['middleware' => 'req.permission:edit_eventTag', 'as' => 'eventTag.update', 'uses' => 'Catalogs\EventTagController@update']);
        $router->delete('/eventTag/{id}', ['middleware' => 'req.permission:delete_eventTag', 'as' => 'eventTag.delete', 'uses' => 'Catalogs\EventTagController@destroy']);
    }
);
