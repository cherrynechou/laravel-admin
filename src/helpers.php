<?php

use Illuminate\Support\Str;

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

/**
 * 找到 值
 */
if (! function_exists('search_column_key')) {

    function search_column_key($searches,$marker){

        $index = collect($searches)->search(function($item) use ($marker){
            return Str::endsWith($item, $marker );
        });

        $column = $searches[$index];

        return Str::replace($marker, '', $column);
    }
}



