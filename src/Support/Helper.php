<?php
namespace CherryneChou\Admin\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Str;

class Helper
{
    /**
     * 把给定的值转化为数组.
     *
     * @param $value
     * @param  bool  $filter
     * @return array
     */
    public static function array($value, bool $filter = true): array
    {
        if ($value === null || $value === '' || $value === []) {
            return [];
        }

        if ($value instanceof \Closure) {
            $value = $value();
        }

        if (is_array($value)) {}
        elseif ($value instanceof Jsonable) {
            $value = json_decode($value->toJson(), true);
        } elseif ($value instanceof Arrayable) {
            $value = $value->toArray();
        } elseif (is_string($value)) {

            $array = null;

            try {
                $array = json_decode($value, true);
            } catch (\Throwable $e) {
            }

            $value = is_array($array) ? $array : explode(',', $value);
        } else {
            $value = (array) $value;
        }

        return $filter ? array_filter($value, function ($v) {
            return $v !== '' && $v !== null;
        }) : $value;
    }

    /**
     * @param $list
     * @param string $pk
     * @param string $pid
     * @param string $children
     * @param int $root
     * @return array
     */
    public static function listToTree($list, $children = 'children', $pk='id', $pid = 'parent_id', $root=0): array
    {
        // 创建Tree
        $tree = [];

        // 创建基于主键的数组引用
        $refer = [];
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }

        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$children][] =& $list[$key];
                }
            }
        }


        return $tree;
    }

    /**
     * 删除数组中的元素.
     *
     * @param  array  $array
     * @param  mixed  $value
     * @param  bool  $strict
     */
    public static function deleteByValue(&$array, $value, bool $strict = false): void
    {
        $value = (array) $value;

        foreach ($array as $index => $item) {
            if (in_array($item, $value, $strict)) {
                unset($array[$index]);
            }
        }
    }

    /**
     * @param  string  $name
     * @param  string  $symbol
     * @return mixed
     */
    public static function slug(string $name, string $symbol = '-')
    {
        $text = preg_replace_callback('/([A-Z])/', function ($text) use ($symbol) {
            return $symbol.strtolower($text[1]);
        }, $name);

        return str_replace('_', $symbol, ltrim($text, $symbol));
    }

    /**
     * 匹配请求路径.
     *
     * @example
     *      Helper::matchRequestPath(admin_base_path('auth/user'))
     *      Helper::matchRequestPath(admin_base_path('auth/user*'))
     *      Helper::matchRequestPath(admin_base_path('auth/user/* /edit'))
     *      Helper::matchRequestPath('GET,POST:auth/user')
     *
     * @param  string  $path
     * @param  null|string  $current
     * @return bool
     */
    public static function matchRequestPath($path, ?string $current = null)
    {
        $request = request();
        $current = $current ?: $request->decodedPath();

        if (Str::contains($path, ':')) {
            [$methods, $path] = explode(':', $path);

            $methods = array_map('strtoupper', explode(',', $methods));

            if (! empty($methods) && ! in_array($request->method(), $methods)) {
                return false;
            }
        }

        // 判断路由名称
        if ($request->routeIs($path) || $request->routeIs(admin_route_name($path))) {
            return true;
        }

        if (! Str::contains($path, '*')) {
            return $path === $current;
        }

        $path = str_replace(['*', '/'], ['([0-9a-z-_,])*', "\/"], $path);

        return preg_match("/$path/i", $current);
    }
}
