<?php
namespace CherryneChou\Admin;

use Illuminate\Support\ServiceProvider;
use CherryneChou\Admin\Support\Helper;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $commands = [
        Console\InstallCommand::class,
        Console\ResetPasswordCommand::class,
    ];

    /**
     * @var array
     */
    protected $routeMiddleware = [
        'admin.permission' => Http\Middleware\Permission::class,
    ];

    /**
     * @var array
     */
    protected $middlewareGroups = [
        'admin' => [
            'admin.permission',
        ],
    ];



    public function register()
    {
        $this->registerRouteMiddleware();
        $this->commands($this->commands);
    }

    public function boot()
    {
        $this->ensureHttps();
    }

    /**
     * 是否强制使用https.
     *
     * @return void
     */
    protected function ensureHttps()
    {
        if (config('admin.https') || config('admin.secure')) {
            \URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', true);
        }
    }

    /**
     * 路由中间件注册.
     *
     * @return void
     */
    protected function registerRouteMiddleware()
    {
        $router = $this->app->make('router');

        // register route middleware.
        foreach ($this->routeMiddleware as $key => $middleware) {
            $router->aliasMiddleware($key, $middleware);
        }

        $disablePermission = ! config('admin.permission.enable');

        // register middleware group.
        foreach ($this->middlewareGroups as $key => $middleware) {
            if ($disablePermission) {
                Helper::deleteByValue($middleware, 'admin.permission', true);
            }
            $router->middlewareGroup($key, $middleware);
        }
    }

}