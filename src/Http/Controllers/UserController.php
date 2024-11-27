<?php

namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Filters\AdministratorFilter;
use CherryneChou\Admin\Serializer\DataArraySerializer;
use CherryneChou\Admin\Transformers\AdministratorTransformer;
use CherryneChou\Admin\Models\Administrator;
use CherryneChou\Admin\Traits\RestfulResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use RestfulResponse;

    /**
     * @param AdministratorFilter $filter
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
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

    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function store()
    {
        $validator = $this->validateForm();

        if($validator->fails()){
            $warning = $validator->messages()->first();
            return $this->failed($warning);
        }

        $requestData = request()->only(['name','username','avatar']);
        $password = request()->input('password');

        if(!empty($password)){
            $requestData['password'] = Hash::make($password);
        }

        $roles = request()->input('roles') ?: [];

        $permissions = request()->input('permissions') ?: '';

        try {
            DB::beginTransaction();

            $user = Administrator::create($requestData);

            if(count($roles)>0){
                $user->roles()->sync($roles);
            }

            if($permissions){
                $permissionIds = json_decode($permissions,true);
                $user->permissions()->sync($permissionIds);
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
        $resource = Administrator::query()->find($id);
        $admin = fractal()
            ->item($resource)
            ->transformWith(new AdministratorTransformer())
            ->serializeWith(new DataArraySerializer())
            ->parseIncludes(['roles','permissions'])
            ->toArray();

        return $this->success($admin);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function update($id)
    {
        $requestData = request()->only(['name','username','avatar']);
        $password = request()->input('password');

        if(!empty($password)){
            $requestData['password'] = Hash::make($password);
        }

        $roles = request()->input('roles') ?: [];

        $permissions = request()->input('permissions') ?: '';

        try {
            DB::beginTransaction();

            $user = Administrator::query()->find($id);
            $user->update($requestData);

            if(count($roles)>0){
                $user->roles()->sync($roles);
            }

            if($permissions){
                $permissionIds = json_decode($permissions,true);
                $user->permissions()->sync($permissionIds);
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
            'username'  => [
                'required',
                Rule::unique(config('admin.database.users_model'))->where('username',request()->input('username'))
            ],
            'password'  => 'required', 
            'roles'     => 'required',
        ];

        $message = [
            "required"              => trans('validation.attribute_not_empty'),
            'username.unique'       => trans('validation.attribute_exists')
        ];

        $attributes = [
            'name'                  => trans('admin.account.name'),
            'password'              => trans('admin.account.password'),
            'username'              => trans('admin.account.username'),
            'roles'                 => trans('admin.account.role')
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
     * 修改密码
     * @param $id
     */
    public function changePassword($id)
    {
        $admin = Administrator::find($id);

        //判断用户密码是否正确
        if (!$admin || !Hash::check(request()->oldPassword, $admin->password)) {
            return $this->failed(trans('admin.origin_password_is_wrong'));
        }

        $newPassword = request()->input('newPassword');

        try {
            DB::beginTransaction();

            $admin->password = Hash::make($newPassword);
            $admin->save();

            DB::commit();

            return $this->success();

        }catch (\Exception $exception){
            DB::rollBack();

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * 重置用户密码
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function resetPassword($id): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $admin = Administrator::find($id);
        $admin->password = Hash::make(config('admin.default_password'));
        $admin->save();

        return $this->success();

    }
}