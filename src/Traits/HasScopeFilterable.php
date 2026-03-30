<?php
namespace CherryneChou\Admin\Traits;

use CherryneChou\Admin\Abstracts\QueryFilter;

trait HasScopeFilterable
{
	/**
     * @param $query
     * @param QueryFilter $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}