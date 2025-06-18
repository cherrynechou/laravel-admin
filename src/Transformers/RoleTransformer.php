<?php
namespace CherryneChou\Admin\Transformers;

use CherryneChou\Admin\Models\Role;
use Illuminate\Support\Carbon;
use League\Fractal\TransformerAbstract;
use Illuminate\Contracts\Support\Arrayable;

class RoleTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [
        //
        'permissions'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Role $model)
    {
        return [
            //
            'id'                =>      $model->id,
            'name'              =>      $model->name,
            'is_administrator'  =>      $model->isAdministrator(),    //是否是管理员
            'slug'              =>      $model->slug,
            'order'             =>      $model->order,
            'status'            =>      $model->status,
            'created_at'        =>      Carbon::parse($model->created_at)->toDateTimeString(),
            'updated_at'        =>      Carbon::parse($model->updated_at)->toDateTimeString(),
        ];
    }


    /**
     * @param Role $model
     * @return \League\Fractal\Resource\Collection
     */
    public function includePermissions(Role $model)
    {
        return $this->collection($model->permissions, new PermissionTransformer(), '');
    }
}