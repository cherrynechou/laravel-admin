<?php
namespace CherryneChou\Admin\Filters;

use CherryneChou\Admin\Abstracts\QueryFilter;

class DepartmentFilter extends QueryFilter
{
	/**
     * @param $slug
     * @return mixed
     */
    public function name($name)
    {
        return $this->builder->where('name', 'like', "%{$name}%");
    }
}