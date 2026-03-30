<?php

namespace CherryneChou\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use CherryneChou\Admin\Traits\HasModelTreeAttributes;
use CherryneChou\Admin\Traits\HasScopeFilterable;
use CherryneChou\Admin\Traits\WithAttributes;

class Department extends Model
{
    use HasModelTreeAttributes,
        HasScopeFilterable,
        WithAttributes;

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


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Department::class, getParentColumn())->orderBy(getOrderColumn())->with('children');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }


    public function findFollowDepartments(int|array $id): array
    {
        if(!is_array($id)){
            $id = [$id];
        }

        $followDepartmentIds = $this->whereIn(getParentColumn(), $id)->pluck(getIdColumn())->toArray();

        if (! empty($followDepartmentIds)) {
            $followDepartmentIds = array_merge($followDepartmentIds, $this->findFollowDepartments($followDepartmentIds));
        }

        return $followDepartmentIds;
    }

}
