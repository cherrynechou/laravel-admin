<?php

namespace CherryneChou\Admin\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use CherryneChou\Admin\Http\Auth\Permission as Checker;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * 权限 验证中间件
 */
class Permission
{
    /**
     * @var string
     */
    protected $middlewarePrefix = 'admin.permission:';

    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     * @throws AuthorizationException
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        if (
            !$user
            || ! config('admin.permission.enable')
            || $user->isAdministrator()
            || $this->checkRoutePermission($request)
        ){
            return $next($request);
        }

        if (! $user->allPermissions()->first(function ($permission) use ($request) {
            return $permission->shouldPassThrough($request);
        })) {
            return response('无权限！', 403);
        }

        return $next($request);
    }

    /**
     * If the route of current request contains a middleware prefixed with 'admin.permission:',
     * then it has a manually set permission middleware, we need to handle it first.
     *
     * @param  Request  $request
     * @return bool
     */
    public function checkRoutePermission(Request $request)
    {
        if (! $middleware = collect($request->route()->middleware())->first(function ($middleware) {
            return Str::startsWith($middleware, $this->middlewarePrefix);
        })) {
            return false;
        }

        $args = explode(',', str_replace($this->middlewarePrefix, '', $middleware));

        $method = array_shift($args);

        if (!method_exists(Checker::class, $method)) {
            abort(500, "Invalid permission method [$method].");
        }

        call_user_func_array([Checker::class, $method], [$args]);

        return true;
    }
}
