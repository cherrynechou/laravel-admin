<?php
namespace CherryneChou\Admin\Traits;

/**
 * 过滤掉空或者null的数据
 */
trait HasFilterData
{
    public function filterNullData(array $input=[])
    {
        return array_filter($input, function ($v) {
            return !is_null($v);
        });
    }
}
