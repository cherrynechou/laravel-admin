<?php
namespace CherryneChou\Admin\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

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
    public static function listToTree($list, $children = 'children', $pk='id', $pid = 'parent_id', $root=0)
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
}