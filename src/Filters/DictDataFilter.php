<?php
namespace CherryneChou\Admin\Filters;

use CherryneChou\Admin\Abstracts\QueryFilter;

class DictDataFilter extends QueryFilter
{
	/**
     * @param $dictId
     * @return mixed
     */
	public function dictId($dictId)
	{
		return $this->builder->where('dict_id', $dictId);
	}


	/**
     * @param $label
     * @return mixed
     */
    public function label($label)
    {
        return $this->builder->where('label', 'like', "%{$label}%");
    }

    /**
     * @param $slug
     * @return mixed
     */
    public function value($value)
    {
        return $this->builder->where('value', 'like', "%{$value}%");
    }
}