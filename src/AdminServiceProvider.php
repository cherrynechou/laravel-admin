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
        Console\PublishCommand::class,
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
        if (file_exists($routes = admin_path('routes.php'))) {
            $this->loadRoutesFrom($routes);
        }

        $this->ensureHttps();
        $this->registerPublishing();
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
     * 资源发布注册.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config' => config_path()], 'laravel-admin-config');
            $this->publishes([__DIR__.'/../resources/lang' => lang_path()], 'laravel-admin-lang');
            $this->publishes([__DIR__.'/../database/migrations' => database_path('migrations')], 'laravel-admin-migrations');
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