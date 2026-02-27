<?php
namespace CherryneChou\Admin\Filters;

use CherryneChou\Admin\Abstracts\QueryFilter;

class PostFilter extends QueryFilter
{
	/**
     * @param $slug
     * @return mixed
     */
    public function name($name)
    {
        return $this->builder->where('name', 'like', "%{$name}%");
    }


    public function code($code)
    {
        return $this->builder->where('code', 'like', "%{$code}%");
    }

}