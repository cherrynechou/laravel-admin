<?php

namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Filters\AdministratorFilter;
use CherryneChou\Serializer\DataArraySerializer;
use CherryneChou\Admin\Transformers\AdministratorTransformer;
use CherryneChou\Admin\Models\Administrator;
use CherryneChou\Admin\Traits\RestfulResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    use RestfulResponse;

    public function index(AdministratorFilter $filter)
    {
        $adminPaginator = Administrator::filter($filter)->paginate();

        $resources = $adminPaginator->getCollection();

        $admins = fractal()
            ->collection($resources)
            ->transformWith(new AdministratorTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($adminPaginator))
            ->parseIncludes(['roles'])
            ->toArray();

        return $this->success($admins);
    }

    public function store()
    {
        $validator = $this->validateForm();

        if($validator->failed()){
            return $this->failed($validator->messages());
        }

        $requestData = request()->only(['name','username','avatar']);
        $password = request()->input('password') || '';

        if(!empty($password)){
            $requestData['password'] = bcrypt($password);
        }

        $roles = request()->input('roles') ?: [];


        try {
            DB::beginTransaction();

            $user = Administrator::create($requestData);

            if(count($roles)>0){
                $user->roles()->sync($roles);
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
        $resource = Administrator::query()->find($id);
        $admin = fractal()
            ->item($resource)
            ->transformWith(new AdministratorTransformer())
            ->serializeWith(new DataArraySerializer())
            ->parseIncludes(['roles'])
            ->toArray();

        return $this->success($admin);
    }

    public function update($id)
    {
        $validator = $this->validateForm();

        if($validator->failed()){
            return $this->failed($validator->messages());
        }

        $requestData = request()->only(['name','username','avatar']);
        $password = request()->input('password') || '';

        if(!empty($password)){
            $requestData['password'] = bcrypt($password);
        }

        $roles = request()->input('roles') ?: [];

        try {
            DB::beginTransaction();

            $user = Administrator::update($requestData,$id);

            if(count($roles)>0){
                $user->roles()->sync($roles);
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
            'username'  => 'required',
            'password'  => 'required',
            'roles'     => 'required',
        ];

        $message = [
            "required"      => ":attribute 不能为空",
        ];

        $attributes = [
            'name'                  => '名称',
            'username'              => '用户名',
            'password'              => '密码',
            'roles'                 => '角色'
        ];

        return Validator::make(request()->all(), $rules, $message, $attributes);
    }

    /**+
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            Administrator::destroy($id);

            DB::commit();

            return $this->success();

        }catch (\Exception $exception){
            DB::rollBack();

            return $this->failed($exception->getTraceAsString());
        }

    }

    /**
     * 禁止登录
     * @param $id
     */
    public function block($id)
    {
        $admin = Administrator::find($id);

        $admin->status = !$admin->status;
        $admin->save();

        return $this->success();
    }

    /**
     * 重置用户密码
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function resetPassword($id)
    {
        $admin = Administrator::find($id);
        $admin->password = bcrypt(config('admin.default_password'));
        $admin->save();

        return $this->success();

    }
}