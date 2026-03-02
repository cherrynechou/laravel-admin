<?php
namespace CherryneChou\Admin\Traits;

/**
 * 过滤掉空或者null的数据
 */
trait HasFilterData
{
    public function filterEmptyOrNullData(array $input=[])
    {
        $filtered = array_filter($input, function ($v) {
            return !is_null($v);
        });

        // 转换 null 字段为空字符串
        foreach (array_keys($input) as $key) {
            if (!isset($data[$key])) {
                $data[$key] = '';
                continue;
            }
            if (is_null($data[$key])) {
                $data[$key] = '';
            }
        }

        return $data;
    }
}
