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



