<?php

namespace CherryneChou\Admin\Models;

use CherryneChou\Admin\Abstracts\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Department extends Model
{
 	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $connection = config('admin.database.connection') ?: config('database.default');
        $this->setConnection($connection);
        $this->setTable(config('admin.database.departments_table'));
    }


    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
