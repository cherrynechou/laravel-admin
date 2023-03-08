<?php
namespace CherryneChou\Admin;

use Illuminate\Routing\Router;

class Admin
{
    const VERSION = '1.0.0';

    /**
     * Register the laravel-admin builtin routes.
     *
     * @return void
     */
    public function routes()
    {
        $attributes = [
            'prefix'     => config('admin.route.prefix'),
            'middleware' => config('admin.route.middleware'),
            'as'         => config('admin.route.prefix')
        ];

        app('router')->group($attributes, function ($router) {

            /* @var \Illuminate\Support\Facades\Route $router */
            $router->namespace('\CherryneChou\Admin\Http\Controllers')->group(function ($router) {

                //权限管理
                $router->group([
                    'prefix'        => 'auth',
                    'namespace'     => 'Auth'
                ],function(Router $router){
                    //用户管理
                    $router->resources([
                        'users'         =>          UserController::class,
                        'roles'         =>          RoleController::class,
                        'permissions'   =>          PermissionController::class,
                        'menu'          =>          MenuController::class,
                    ], [
                        'only' => ['index','store', 'show' , 'update', 'destroy']
                    ]);

                    //非分页列表
                    $router->get('/role/all','RoleController@all')->name('roles.all');

                    //非树型结构列表
                    $router->get('/permission/all','PermissionController@all')->name('permissions.all');

                    //获取权限路由
                    $router->get('/permission/routes','PermissionController@routes')->name('permissions.routes');

                    //更改菜单的状态
                    $router->patch('/menu/{menu}/switch','MenuController@switchStatus');

                    //重置用户密码
                    $router->patch('/user/{user}/resetPassword','UserController@resetPassword');

                    //阻止用户登录
                    $router->patch('/user/{user}/block','UserController@block');
                });

            });


            //登录
            $authController = config('admin.auth.controller', AuthController::class);
            /* @var \Illuminate\Routing\Router $router */
            $router->post('auth/login', $authController . '@postLogin');
            //当前用户
            $router->get('/currentUser', $authController . '@currentUser');
            //菜单
            $router->get('/getMenuList', $authController . '@getMenuList');



        });
    }
}