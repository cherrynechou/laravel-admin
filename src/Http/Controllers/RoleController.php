<?php

namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Filters\RoleFilter;
use CherryneChou\Admin\Models\Role;
use CherryneChou\Admin\Serializer\DataArraySerializer;
use CherryneChou\Admin\Transformers\RoleTransformer;
use CherryneChou\Admin\Traits\RestfulResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    use RestfulResponse;

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

        // 获取通过验证的数据...
        $validated = $validator->safe()->only(['name', 'slug', 'order', 'status']);

        $permissions = request()->input('permissions') ?: '';

        try {

            DB::beginTransaction();

            $role = Role::create($validated);

            if($permissions){
                $permissionIds = json_decode($permissions,true);
                $role->permissions()->sync($permissionIds);
            }

            DB::commit();

            return $this->success();

        }catch (\Exception $exception){

            DB::rollBack();

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function show($id)
    {
        $resource = Role::query()->find($id);

        $role = fractal()
            ->item($resource)
            ->transformWith(new RoleTransformer())
            ->serializeWith(new DataArraySerializer())
            ->parseIncludes(['permissions'])
            ->toArray();

        return $this->success($role);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function update($id)
    {

        $requestData = request()->only(['name', 'slug', 'order', 'status']);

        $permissions = request()->input('permissions') ?: '';

        try {
            DB::beginTransaction();

            $role = Role::query()->find($id);
            $role->update($requestData);

            if($permissions){
                $permissionIds = json_decode($permissions,true);
                $role->permissions()->sync($permissionIds);
            }

            DB::commit();

            return $this->success();

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
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            Role::destroy($id);

            DB::commit();

            return $this->success();

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
}