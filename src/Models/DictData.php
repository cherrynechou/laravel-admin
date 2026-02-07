<?php

namespace CherryneChou\Admin\Models;

use CherryneChou\Admin\Abstracts\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class DictData.
 *
 * @package namespace App\Models;
 */
class DictData extends Model 
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
        $this->setTable(config('admin.database.dict_data_table'));
       
    }


    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    /**
     *  作用域限制为仅包含给定类型的用户
     */
    public function scopeOfCode(Builder $query, string $code): void
    {
        $query->where('code', $code);
    }

}
