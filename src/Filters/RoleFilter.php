<?php
namespace CherryneChou\Admin\Filters;

use CherryneChou\Admin\Abstracts\QueryFilter;

class RoleFilter extends QueryFilter
{
    /**
     * @param $name
     * @return mixed
     */
    public function name($name)
    {
        return $this->builder->where('name', 'like', "%{$name}%");
    }

    /**
     * @param $slug
     * @return mixed
     */
    public function slug($slug)
    {
        return $this->builder->where('slug', 'like', "%{$slug}%");
    }
}