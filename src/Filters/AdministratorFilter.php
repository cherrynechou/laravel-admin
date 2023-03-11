<?php
namespace CherryneChou\Admin\Filters;

use CherryneChou\Admin\Abstracts\QueryFilter;

class AdministratorFilter extends QueryFilter
{
    /**
     * @param $username
     * @return mixed
     */
    public function username($username)
    {
        return $this->builder->where('username', 'like', "%{$username}%");
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