<?php
namespace CherryneChou\Admin\Filters;

use CherryneChou\Admin\Abstracts\QueryFilter;

class ConfigGroupFilter extends QueryFilter
{
	/**
     * @param $groupId
     * @return mixed
     */
    public function groupId($groupId)
    {
        return $this->builder->where('group_id', $groupId);
    }
}