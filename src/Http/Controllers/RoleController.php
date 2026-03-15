<?php

namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Filters\RoleFilter;
use CherryneChou\Admin\Models\Role;
use CherryneChou\Admin\Serializer\DataArraySerializer;
use CherryneChou\Admin\Transformers\RoleTransformer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Illuminate\Validation\Rule;

class RoleController extends BaseController
{
    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function index(RoleFilter $filter)
    {
        $rolePaginator = Role::query()->filter($filter)->paginate();

        $resources = $rolePaginator->getCollection();

        $roles = fractal()
                    ->collection($resources)
                    ->transformWith(new RoleTransformer())
                    ->paginateWith(new IlluminatePaginatorAdapter($rolePaginator))
                    ->toArray();

        return $this->success($roles);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function store()
    {
        $validator = $this->validateForm();

        if($validator->fails()){
            $warning =$validator->messages()->first();
            return $this->failed($warning);
        }

        $requestData = request()->all();
        try {
            DB::beginTransaction();
            $role = Role::create($requestData);
            DB::commit();
            return $this->success([], trans('admin.save_succeeded'));
        }catch (\Exception $exception){
            DB::rollBack();
            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function show(string $id)
    {
        $resource = Role::query()->find($id);

        $role = fractal()
            ->item($resource)
            ->transformWith(new RoleTransformer())
            ->serializeWith(new DataArraySerializer())
            ->toArray();

        return $this->success($role);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function update(string $id)
    {
        $validator = $this->validateForm();

        if($validator->fails()){
            $warning =$validator->messages()->first();
            return $this->failed($warning);
        }

        $requestData = request()->all();

        try {
            DB::beginTransaction();

            $role = Role::query()->find($id);
            $role->update($requestData);

            DB::commit();

            return $this->success([], trans('admin.update_succeeded'));

        }catch (\Exception $exception){
            DB::rollBack();

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @return \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     */
    protected function validateForm()
    {
        $rules = [
           'name'      => [
                'required',
                Rule::unique(config('admin.database.roles_model'))->where('name',request()->input('name'))
            ],
            'slug'      => [
                'required',
                Rule::unique(config('admin.database.roles_model'))->where('slug',request()->input('slug'))
            ],
        ];

        $message = [
            'required'              => trans('validation.attribute_not_empty'),
            'name.unique'           => trans('validation.attribute_exists'),
            'slug.unique'           => trans('validation.attribute_exists')
        ];

        $attributes = [
            'name'       => trans('admin.role.name'),
            'slug'       => trans('admin.role.slug'),
        ];

        return Validator::make(request()->all(), $rules, $message, $attributes);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            Role::destroy($id);
            DB::commit();
            return $this->success([], trans('admin.delete_succeeded'));
        }catch (\Exception $exception){
            DB::rollBack();
            return $this->failed($exception->getTraceAsString());
        }
    }

    /**
     * 角色列表
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function all()
    {
        $resources = Role::query()->get();

        $roles =  fractal()
                ->collection($resources)
                ->transformWith(new RoleTransformer())
                ->serializeWith(new DataArraySerializer())
                ->toArray();

        return $this->success($roles);
    }

    /**
     * 角色所有权限列表
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function permissions(string $id)
    {
        $currentRole = Role::query()->find($id);
        return $this->success($currentRole->permissions ?? []);
    }

    /**
     * 更新角色权限
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function updatePermissions(string $id)
    {
        $permissions = request()->input('permissionIds') ?: '';

        try {
            DB::beginTransaction();
            $role = Role::query()->find($id);

            if($permissions){
                $permissionIds = json_decode($permissions,true);
                $role->permissions()->sync($permissionIds);
            }else{
                $role->permissions()->sync([]);
            }

            DB::commit();

            return $this->success([], trans('admin.delete_succeeded'));
        }catch (\Exception $exception){
            DB::rollBack();

            return $this->failed($exception->getTraceAsString());
        }
    }

    /**
     * 角色所有数据权限
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public  function dataScopes(string $id)
    {
        $currentRole = Role::query()->find($id);
        $return['departments'] = $currentRole->departments ?? [];
        $return['name'] = $currentRole->name;
        $return['slug'] = $currentRole->slug;
        $return['data_scope'] = $currentRole->data_scope;
        return $this->success($return);
    }

    /**
     * 更新角色数据权限
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function updateDataScopes(string $id)
    {
        $requestData = request()->all();

        $departments = request()->input('departmentIds') ?: '';

        try{
            DB::beginTransaction();
            $role = Role::query()->find($id);

            $role->update($requestData);

            if($departments){
                $departmentIds = json_decode($departments,true);
                $role->departments()->sync($departmentIds);
            }else{
                $role->departments()->sync([]);
            }

            DB::commit();

            return $this->success([], trans('admin.update_succeeded'));

        }catch (\Exception $exception){
            DB::rollBack();

            return $this->failed($exception->getTraceAsString());
        }
    }


}