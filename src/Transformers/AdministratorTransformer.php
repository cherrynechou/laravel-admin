<?php
namespace CherryneChou\Admin\Transformers;

use CherryneChou\Admin\Models\Administrator;
use Illuminate\Support\Carbon;
use League\Fractal\TransformerAbstract;
use Illuminate\Contracts\Support\Arrayable;

class AdministratorTransformer extends TransformerAbstract
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
         'roles','permissions'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Administrator $model)
    {
        return [
            //
            'id'                =>          $model->id,
            'username'          =>          $model->username,
            'phone'             =>          $model->phone,
            'name'              =>          $model->name,
            'email'             =>          $model->email,
            'avatar'            =>          $model->avatar,
            'avatar_url'        =>          $model->getAvatar(),
            'login_count'       =>          $model->login_count,
            'last_login_ip'     =>          $model->last_login_ip,
            'last_login_time'   =>          Carbon::parse($model->last_login_time)->toDateTimeString(),
            'is_administrator'  =>          $model->isAdministrator(),    //是否是管理员
            'status'            =>          $model->status,
            'created_at'        =>          Carbon::parse($model->created_at)->toDateTimeString(),
            'updated_at'        =>          Carbon::parse($model->updated_at)->toDateTimeString(),
        ];
    }

    /**
     * @param Administrator $model
     * @return \League\Fractal\Resource\Collection
     */
    public function includeRoles(Administrator $model)
    {
        return $this->collection($model->roles, new RoleTransformer(), '');
    }


    /**
     * @param Administrator $model
     * @return \League\Fractal\Resource\Collection
     */
    public function includePermissions(Administrator $model)
    {
        return $this->collection($model->permissions, new PermissionTransformer(),'');
    }
}