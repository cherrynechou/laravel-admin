<?php
namespace CherryneChou\Admin;

class Admin
{
    const VERSION = '2.0.2';

    /**
     * 应用管理.
     *
     * @return Application
     */
    public static function app()
    {
        return app('admin.app');
    }


    /**
     * Register the laravel-admin builtin routes.
     *
     * @return void
     */
    public function routes()
    {
        $router = app('router');

        $attributes = [
            'prefix'     => config('admin.route.prefix'),
            'middleware' => config('admin.route.middleware'),
        ];

        $router->group($attributes, function ($router) {
            //登录
            $authController = config('admin.auth.controller', AuthController::class);
            /* @var \Illuminate\Routing\Router $router */
            $router->post('/oauth/login', $authController . '@postLogin')->name('oauth.login');
        });


        $auth_attributes = [
            'prefix'     => config('admin.route.prefix'),
            'middleware' => config('admin.route.auth_middleware'),
        ];

        $router->group($auth_attributes, function ($router) {

            if (config('admin.auth.enable', true)) {
                /* @var \Illuminate\Support\Facades\Route $router */
                $router->namespace('\CherryneChou\Admin\Http\Controllers')->group(function ($router) {

                    /* @var \Illuminate\Routing\Router $router */
                    $router->resource('auth/users', 'UserController',['except'=>['create','edit']])->names('auth.users');
                    $router->resource('auth/roles', 'RoleController',['except'=>['create','edit']])->names('auth.roles');
                    $router->resource('auth/permissions', 'PermissionController',['except'=>['create','edit']])->names('auth.permissions');
                    $router->resource('auth/menu', 'MenuController', ['except' => ['create','edit']])->names('auth.menu');

                    //非分页列表
                    $router->get('/role/all','RoleController@all')->name('roles.all');

                    //非树型结构列表
                    $router->get('/permission/all','PermissionController@all')->name('permissions.all');

                    //获取权限路由
                    $router->get('/permission/routes','PermissionController@routes')->name('permissions.routes');

                    //更改菜单的状态
                    $router->patch('/menu/{menu}/switch','MenuController@switchStatus')->name('menu.switch');

                    //重置用户密码
                    $router->patch('/user/{user}/resetPassword','UserController@resetPassword')->name('user.reset.password');

                    //阻止用户登录
                    $router->patch('/user/{user}/block','UserController@block')->name('user.block');
                });
            }

            //登录
            $authController = config('admin.auth.controller', AuthController::class);
            //当前用户
            $router->get('/currentUser', $authController . '@currentUser')->name('current.user');
            //菜单
            $router->get('/getMenuList', $authController . '@getMenuList')->name('menu.list');
        });
    }
}