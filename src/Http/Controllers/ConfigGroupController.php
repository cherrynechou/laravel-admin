<?php

namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Models\ConfigGroup;
use CherryneChou\Admin\Serializer\DataArraySerializer;
use CherryneChou\Admin\Transformers\ConfigGroupTransformer;
use CherryneChou\Admin\Filters\ConfigGroupFilter;
use Illuminate\Support\Facades\Validator;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Illuminate\Support\Facades\DB;

class ConfigGroupController extends BaseController
{
	 
	public function index(ConfigGroupFilter $filter): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $groupPaginator = ConfigGroup::filter($filter)->orderBy('sort')->paginate();

        $resources = $groupPaginator->getCollection();

        $groups = fractal()
            ->collection($resources)
            ->transformWith(new ConfigGroupTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($groupPaginator))
            ->toArray();

        return $this->success($groups);
    }


    public function store(): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $validator = $this->validateForm();
        if($validator->fails()){
            $warning = $validator->messages()->first();
            return $this->failed($warning);
        }
        $requestData = request()->all();
        try {
            DB::beginTransaction();
            ConfigGroup::create($requestData);
            DB::commit();
            return $this->success([], trans('admin.save_succeeded'));
        }catch (\Exception $exception){
            DB::commit();
            return $this->failed($exception->getMessage());
        }

    }

    public function show(string $id): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        //获取资源
        $resource =  ConfigGroup::query()->find($id);

        $config = fractal()
            ->item($resource)
            ->transformWith(new ConfigGroupTransformer())
            ->serializeWith(new DataArraySerializer())
            ->toArray();

        return $this->success($config);

    }

    public function update(string $id): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $validator = $this->validateForm();

        if($validator->fails()){
            $warning = $validator->messages()->first();
            return $this->failed($warning);
        }

        $requestData = request()->all();
        try {
            DB::beginTransaction();
            $group = ConfigGroup::query()->find($id);
            $group->update($requestData);
            DB::commit();
            return $this->success([], trans('admin.update_succeeded'));
        }catch (\Exception $exception){
            DB::commit();
            return $this->failed($exception->getMessage());
        }

    }

    /**
     * @return \Illuminate\Validation\Validator
     */
    protected function validateForm()
    {
        $rules =[
            'name'      => 'required',
            'key'       => 'required',
        ];

        $message = [
            'required'   => trans('validation.attribute_not_empty')
        ];

        $attributes = [
            'name'      => trans('admin.config.group_name'),
            'key'      => trans('admin.config.group_key')    
        ];

        return Validator::make(request()->all(), $rules, $message, $attributes);
    }

    public function destroy(string $id): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        //
        try {
            DB::beginTransaction();
            ConfigGroup::destroy($id);
            DB::commit();
            return $this->success([], trans('admin.delete_succeeded'));
        }catch (\Exception $exception){
            DB::rollBack();
            return $this->failed($exception->getTraceAsString());
        }

    }

}