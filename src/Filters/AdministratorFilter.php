<?php
namespace CherryneChou\Admin\Filters;

use CherryneChou\Admin\Abstracts\QueryFilter;

class AdministratorFilter extends QueryFilter
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
     * @param $phone
     * @return mixed
     */
    public function phone($phone)
    {
        return $this->builder->where('phone', 'like', "%{$phone}%");
    }

}