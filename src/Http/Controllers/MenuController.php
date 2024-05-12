<?php

namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Serializer\DataArraySerializer;
use CherryneChou\Admin\Support\Helper;
use CherryneChou\Admin\Transformers\MenuTransformer;
use CherryneChou\Admin\Models\Menu;
use CherryneChou\Admin\Traits\RestfulResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    use RestfulResponse;

    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function index()
    {
        $resources = Menu::query()->orderBy('order')->get();

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

        if($validator->fails()){
            $warning = $validator->messages()->first();
            return $this->failed($warning);
        }

        $requestData = request()->only([
            'name','locale','path','parent_id','target','url','icon','order','status'
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
        $requestData = request()->only([
            'name','locale','path','parent_id','target','url','icon','order','status'
        ]);

        $roles = request()->input('roles') ?: [];

        try {
            DB::beginTransaction();

            $menu = Menu::query()->find($id);

            $menu->update($requestData);

            if(count($roles)>0){
                $menu->roles()->sync($roles);
            }else{
                $menu->roles()->detach();
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
            'name'      => [
                'required',
                Rule::unique(config('admin.database.menu_table'))->where('name',request()->input('name'))
            ],
            'locale'      => [
                'required',
                Rule::unique(config('admin.database.menu_table'))->where('locale',request()->input('locale'))
            ],
            'parent_id' => 'required',
            'path'      => 'required',
            'icon'      => 'required_if:parent_id,0'
        ];

        $message = [
            'required'      => trans('validation.attribute_not_empty'),
            'name.unique'   => trans('validation.attribute_exists'),
            'required_if'   => trans('validation.attribute_root_not_empty'),
        ];

        $attributes = [
            'name'                      => trans('admin.menu.name'),
            'locale'                    => trans('admin.menu.locale'),
            'parent_id'                 => trans('admin.menu.parent_id'),
            'path'                      => trans('admin.menu.path'),
            'icon'                      => trans('admin.menu.icon')
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