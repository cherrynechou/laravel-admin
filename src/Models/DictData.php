<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        $connection = config('admin.database.connection') ?: config('database.default');
        $this->setConnection($connection);
        $this->setTable(config('admin.database.prefix') . 'dict_data');
        parent::__construct($attributes);
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
