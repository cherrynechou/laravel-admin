<?php
namespace CherryneChou\Admin\Transformers;

use CherryneChou\Admin\Models\Menu;
use Illuminate\Support\Carbon;
use League\Fractal\TransformerAbstract;
use Illuminate\Contracts\Support\Arrayable;

class MenuTransformer extends TransformerAbstract
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
        'permissions',
        'roles'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Menu $model)
    {
        return [
            //
            'id'            =>          $model->id,
            'name'          =>          $model->name,
            'key'           =>          $model->key,
            'type'          =>          $model->type,
            'locale'        =>          $model->locale,
            'parent_id'     =>          $model->parent_id,
            'path'          =>          $model->path,
            'is_back_link'  =>          $model->is_back_link,
            'target'        =>          $model->target,
            'uri'           =>          $model->uri,
            'icon'          =>          $model->icon,
            'status'        =>          $model->status,
            'sort'          =>          $model->sort,
            'visible'       =>          $model->visible,
            'created_at'    =>          Carbon::parse($model->created_at)->toDateTimeString(),
            'updated_at'    =>          Carbon::parse($model->updated_at)->toDateTimeString(),
        ];
    }

    /**
     * @param Menu $model
     * @return \League\Fractal\Resource\Collection
     */
    public function includeRoles(Menu $model)
    {
        return $this->collection($model->roles, new RoleTransformer(), '');
    }

    /**
     * @param Menu $model
     * @return \League\Fractal\Resource\Collection
     */
    public function includePermissions(Menu $model)
    {
        return $this->collection($model->permissions, new PermissionTransformer(), '');
    }
}
