<?php

namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Models\Role;
use CherryneChou\Admin\Serializer\DataArraySerializer;
use CherryneChou\Admin\Transformers\RoleTransformer;
use CherryneChou\Admin\Traits\RestfulResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Illuminate\Routing\Controller;

class RoleController extends Controller
{
    use RestfulResponse;

    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function index()
    {
        $rolePaginator = Role::query()->paginate();

        $resources = $rolePaginator->getCollection();

        $roles = fractal()
                    ->collection($resources)
                    ->transformWith(new RoleTransformer())
                    ->paginateWith(new IlluminatePaginatorAdapter($rolePaginator))
                    ->toArray();

        return $this->success($roles);
    }

    public function store()
    {
        $validator = $this->validateForm();

        if($validator->failed()){
            return $this->failed($validator->messages());
        }

        // 获取通过验证的数据...
        $validated = $validator->safe()->only(['name', 'slug']);

        $permissions = request()->input('permissions') ?: '';

        try {

            DB::beginTransaction();

            $role = Role::query()->create($validated);

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
        $validator = $this->validateForm();

        if($validator->failed()){
            return $this->failed($validator->messages());
        }

        // 获取通过验证的数据...
        $validated = $validator->safe()->only(['name', 'slug']);

        $permissions = request()->input('permissions') ?: '';

        try {
            DB::beginTransaction();

            $role = Role::query()->update($validated, $id);

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
            'name'      => 'required',
            'slug'      => 'required',
        ];

        $message = [
            'required'   => ':attribute 不能为空',
        ];

        $attributes = [
            'name'       => '菜单名称',
            'slug'       => '标识名称',
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
        $resources = Role::query()->all();

        $roles =  fractal()
                ->collection($resources)
                ->transformWith(new RoleTransformer())
                ->serializeWith(new DataArraySerializer())
                ->toArray();

        return $this->success($roles);
    }
}