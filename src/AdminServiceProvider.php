<?php
namespace CherryneChou\Admin;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Arr;
use CherryneChou\Admin\Support\Helper;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $commands = [
        Console\InstallCommand::class,
        Console\PublishCommand::class,
        Console\AppCommand::class,
        Console\UninstallCommand::class,
        Console\ResetPasswordCommand::class,
    ];

    /**
     * @var array
     */
    protected $routeMiddleware = [
        'admin.permission'  => Http\Middleware\Permission::class,
        'admin.app'         => Http\Middleware\Application::class,
        'admin.locale'      => Http\Middleware\Locale::class,
    ];

    /**
     * @var array
     */
    protected $middlewareGroups = [
        'admin' => [
            'admin.permission',
            'admin.locale'
        ],
    ];


    public function register()
    {
        $this->aliasAdmin();
        $this->loadAdminAuthConfig();
        $this->registerRouteMiddleware();
        $this->registerServices();
        $this->commands($this->commands);
    }

    public function boot()
    {
        $this->ensureHttps();
        $this->bootApplication();
        $this->registerPublishing();
    }

    protected function aliasAdmin()
    {
        if (! class_exists(\Admin::class)) {
            class_alias(Admin::class, \Admin::class);
        }
    }


    /**
     * 路由注册.
     */
    protected function bootApplication()
    {
        Admin::app()->boot();
    }

    /**
     * Setup auth configuration.
     *
     * @return void
     */
    protected function loadAdminAuthConfig()
    {
        config(Arr::dot(config('admin.auth', []), 'auth.'));

        foreach ((array) config('admin.multi_app') as $app => $enable) {
            if ($enable) {
                config(Arr::dot(config($app.'.auth', []), 'auth.'));
            }
        }
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


    
    public function registerServices()
    {
        $this->app->singleton('admin.app', Application::class);
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
            if (version_compare($this->app->version(), '9.0.0', '>=')) {
                $this->publishes([__DIR__.'/../resources/lang' => base_path('lang')], 'laravel-admin-lang');
            } else {
                $this->publishes([__DIR__.'/../resources/lang' => resource_path('lang')], 'laravel-admin-lang');
            }
            $this->publishes([__DIR__.'/../database/migrations' => database_path('migrations')], 'laravel-admin-migrations');
            $this->publishes([__DIR__.'/../resources/assets' => public_path('vendor/laravel-admin')], 'laravel-admin-assets');
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