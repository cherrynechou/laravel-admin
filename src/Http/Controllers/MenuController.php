<?php

namespace CherryneChou\Admin\Http\Controllers;

use App\Serializer\DataArraySerializer;
use App\Support\Helper;
use App\Transformers\MenuTransformer;
use CherryneChou\Admin\Models\Menu;
use CherryneChou\Admin\Traits\RestfulResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;

class MenuController extends Controller
{
    use RestfulResponse;

    public function index()
    {
        $resources = Menu::query()->orderBy('order','ASC')->all();

        $menuResources = fractal()
                        ->collection($resources)
                        ->transformWith(new MenuTransformer())
                        ->serializeWith(new DataArraySerializer())
                        ->parseIncludes(['roles'])
                        ->toArray();

        $menus = Helper::listToTree($menuResources);

        return $this->success($menus);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function store()
    {
        $validator = $this->validateForm();

        if($validator->failed()){
            return $this->failed($validator->messages());
        }

        $requestData = request()->only([
            'name','path','parent_id','target','url','icon','order','status'
        ]);

        $roles = request()->input('roles') ?: [];

        try {
            DB::beginTransaction();

            $menu = Menu::create($requestData);

            if(count($roles)>0){
                $menu->roles()->sync($roles);
            }

            DB::commit();

            return $this->success([], trans('admin.save_succeeded'));

        }catch (\Exception $exception){

            DB::commit();

            return $this->failed($exception->getMessage());
        }

    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function show($id)
    {
        $resource = Menu::query()->find($id);

        $menu = fractal()
                    ->item($resource)
                    ->transformWith(new MenuTransformer())
                    ->serializeWith(new DataArraySerializer())
                    ->parseIncludes(['roles','permissions'])
                    ->toArray();

        return $this->success($menu);
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

        $requestData = request()->only([
            'name','path','parent_id','target','url','icon','order','status'
        ]);

        $roles = request()->input('roles') ?: [];

        try {
            DB::beginTransaction();

            $menu = Menu::update($requestData, $id);

            if(count($roles)>0){
                $menu->roles()->sync($roles);
            }

            DB::commit();

            return $this->success([],trans('admin.update_succeeded'));

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
            'parent_id' => 'required',
            'path'      => 'required',
        ];

        $message = [
            "required"      => ":attribute 不能为空",
        ];

        $attributes = [
            "name"                      => '菜单名称',
            "parent_id"                 => '菜单父对象',
            "path"                      => '菜单路径',
        ];

        return Validator::make(request()->all(), $rules, $message, $attributes);
    }

    /**
     * 更改菜单状态
     * @param $id
     */
    public function switchStatus($id)
    {
        try {
            DB::beginTransaction();

            $menu = Menu::query()->find($id);

            $menu->status = !$menu->status;
            $menu->save();

            DB::commit();

            return $this->success([],trans('admin.update_succeeded'));

        }catch (\Exception $exception){

            DB::rollBack();

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function destroy($id)
    {

        try {
            DB::beginTransaction();


            Menu::destroy($id);

            DB::commit();

            return $this->success();

        }catch (\Exception $exception){
            DB::rollBack();

            return $this->failed($exception->getTraceAsString());
        }
    }
}