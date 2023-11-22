<?php

use Illuminate\Support\Str;


if (! function_exists('admin_path')) {
    /**
     * Get admin path.
     *
     * @param  string  $path
     * @return string
     */
    function admin_path($path = '')
    {
        return ucfirst(config('admin.directory')).($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (! function_exists('admin_base_path')) {
    /**
     * Get admin url.
     *
     * @param  string  $path
     * @return string
     */
    function admin_base_path($path = '')
    {
        $prefix = '/' . trim(config('admin.route.prefix'), '/');

        $prefix = ($prefix == '/') ? '' : $prefix;

        $path = trim($path, '/');

        if (is_null($path) || strlen($path) == 0) {
            return $prefix ?: '/';
        }

        return $prefix . '/' . $path;
    }
}

if (! function_exists('admin_asset')) {
    /**
     * @param $path
     *
     * @return string
     */
    function admin_asset($path)
    {
        return (config('admin.https') || config('admin.secure')) ? secure_asset($path) : asset($path);
    }
}


if (! function_exists('admin_route')) {
    /**
     * 根据路由别名获取url.
     *
     * @param  string|null  $route
     * @param  array  $params
     * @param  bool  $absolute
     * @return string
     */
    function admin_route(?string $route, array $params = [], $absolute = true)
    {
        return Admin::app()->getRoute($route, $params, $absolute);
    }
}

if (! function_exists('admin_route_name')) {
    /**
     * 获取路由别名.
     *
     * @param  string|null  $route
     * @return string
     */
    function admin_route_name(?string $route)
    {
        return Admin::app()->getRoutePrefix().$route;
    }
}



