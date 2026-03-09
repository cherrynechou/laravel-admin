<?php
namespace CherryneChou\Admin;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Auth;

class Admin
{
    const VERSION = '2.3.0';


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
     * 获取登录用户模型.
     *
     * @return Model|Authenticatable|HasPermissions
     */
    public static function user()
    {
        return static::guard()->user();
    }

    /**
     * @return \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard|GuardHelpers
     */
    public static function guard()
    {
        return Auth::guard(config('admin.auth.guard') ?: 'admin');
    }

    /**
     * @param  callable  $callback
     */
    public static function booting($callback)
    {
        Event::listen('admin:booting', $callback);
    }

    /**
     * @param  callable  $callback
     */
    public static function booted($callback)
    {
        Event::listen('admin:booted', $callback);
    }

    /**
     * @return void
     */
    public static function callBooting()
    {
        Event::dispatch('admin:booting');
    }

    /**
     * @return void
     */
    public static function callBooted()
    {
        Event::dispatch('admin:booted');
    }


    /**
     * Bootstrap the admin application.
     */
    public function bootstrap()
    {
        require config('admin.bootstrap', admin_path('bootstrap.php'));

        $this->fireEvents();
    }

    /**
     * Bootstrap the admin event.
     */
    protected function fireEvents()
    {
        Admin::callBooting();

        Admin::callBooted();
    }


    /**
     * 上下文管理.
     *
     * @return \Dcat\Admin\Support\Context
     */
    public static function context()
    {
        return app('admin.context');
    }


    /**
     * Register the laravel-admin builtin routes.
     *
     * @return void
     */
    public function routes()
    {
        $router = app('router');

        $router->group([
            'prefix'     => config('admin.route.prefix'),
            'middleware' => config('admin.route.middleware'),
        ], function ($router) {
            //登录
            $authController = config('admin.auth.controller', AuthController::class);
            /* @var \Illuminate\Routing\Router $router */
            $router->post('/oauth/login', $authController . '@postLogin')->name('oauth.login');
        });


        $router->group([
            'prefix'     => config('admin.route.prefix'),
            'middleware' => config('admin.route.auth_middleware'),
        ], function ($router) {
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
                    //角色权限
                    $router->get('/role/{id}/permissions','RoleController@permissions')->name('role.permissions');
                    //更新角色权限
                    $router->put('/role/{id}/updatePermissions','RoleController@updatePermissions')->name('role.permissions.update');
                    //非树型结构列表
                    $router->get('/permission/all','PermissionController@all')->name('permissions.all');
                    //获取权限路由
                    $router->get('/permission/routes','PermissionController@routes')->name('permissions.routes');

                    //更改菜单的状态
                    $router->patch('/menu/{menu}/switch','MenuController@switch')->name('menu.switch');
                    //重置用户密码
                    $router->patch('/user/{user}/resetPassword','UserController@resetPassword')->name('user.reset.password');
                    //更改用户密码
                    $router->patch('/user/{user}/changePassword','UserController@changePassword')->name('user.change.password');
                    //阻止用户登录
                    $router->patch('/user/{user}/block','UserController@block')->name('user.block');
                });
            }

            $router->namespace('\CherryneChou\Admin\Http\Controllers')->group(function ($router) {
                /* @var \Illuminate\Routing\Router $router */
                $router->resource('auth/departments', 'DepartmentController', ['except' => ['create','edit']])->names('auth.departments');
                $router->resource('auth/posts', 'PostController', ['except' => ['create','edit']])->names('auth.posts');
                $router->resource('auth/attchment/category', 'AttachmentCategoryController', ['except' => ['create','edit']])->names('auth.department.category');
                $router->resource('auth/attchments', 'AttachmentController', ['except' => ['create','edit']])->names('auth.attchments');

                //操作日志
                $router->get('auth/log/operations','LogController@operationLogs')->name('log.operations');
                $router->get('auth/log/logins','LogController@loginLogs')->name('log.logins');

                //数据字典
                $router->resource('auth/dicts', 'DictController', ['except' => ['create','edit']])->names('auth.dict');    
                $router->resource('auth/dict/datas', 'DictDataController', ['except' => ['create','edit']])->names('auth.dict.data');   

                //部门列表
                $router->get('/department/all','DepartmentController@all')->name('departments.all');
                //所有岗位
                $router->get('/post/all','PostController@all')->name('post.all');

                //配置    
                $router->resource('auth/config/groups', 'ConfigGroupController', ['except' => ['create','edit']])->names('auth.config.group');    
                $router->resource('auth/config/datas', 'ConfigController', ['except' => ['create','edit']])->names('auth.config.data');   

                //获取分组配置
                $router->get('/setting/group/all','SettingController@groups')->name('config.group.all');
                //更新配置
                $router->post('/setting/update/{name}','SettingController@update');

                //option 设置
                $router->get('/setting/config/options/{id}','SettingController@options')->name('config.options');
                $router->get('/setting/config/options/{id}/update','SettingController@saveOptions')->name('config.options.update');

                //获取网站配置
                $router->get('/getWebConfig','SettingControoler@getWebConfig')->name('config.getWebConfig');
             });

           


            //登录
            $authController = config('admin.auth.controller', AuthController::class);
            //获取验证码
            $router->get('/getCaptcha',$authController . '@getCaptcha')->name('getCaptcha');
            //当前用户
            $router->get('/currentUser', $authController . '@currentUser')->name('current.user');
            //菜单
            $router->get('/getMenuList', $authController . '@getMenuList')->name('menu.list');
            //退出    
            $router->get('/oauth/logout', $authController . '@getLogout')->name('oauth.logout');

            $router->namespace('\CherryneChou\Admin\Http\Controllers')->group(function ($router) {
                //图片上传
                $router->any('/upload/imageFiles','UploadController@handleImage')->name('upload.image');
            });
        });
    }
}