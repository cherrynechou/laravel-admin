<?php

namespace CherryneChou\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use CherryneChou\Admin\Abstracts\QueryFilter;

class ConfigGroup extends Model 
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
        $this->setTable(config('admin.database.config_group_table'));
    }

    public function configs()
    {
        return $this->hasMany(Config::class, 'group_id');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}