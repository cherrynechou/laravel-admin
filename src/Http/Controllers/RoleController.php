<?php

namespace CherryneChou\Admin\Http\Controllers;

use App\Serializer\DataArraySerializer;
use App\Transformers\RoleTransformer;
use CherryneChou\Admin\Traits\RestfulResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    use RestfulResponse;

    public function index()
    {

    }

    public function store()
    {

    }

    public function show($id)
    {

    }

    public function update($id)
    {

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