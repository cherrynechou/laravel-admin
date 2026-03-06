<?php

namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Models\ConfigGroup;
use CherryneChou\Admin\Serializer\DataArraySerializer;
use CherryneChou\Admin\Transformers\ConfigGroupTransformer;
use CherryneChou\Admin\Contracts\ValidatorInterface;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Illuminate\Support\Facades\DB;

class ConfigGroupController extends BaseController
{
	 protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'name'      => 'required',
            'key'       => 'required',
        ],
        ValidatorInterface::RULE_UPDATE => [
            'name'      => 'required',
            'key'       => 'required',
        ]
    ];

	public function index(): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $groupPaginator = ConfigGroup::query()->orderBy('sort')->paginate();

        $resources = $groupPaginator->getCollection();

        $groupDatas = fractal()
            ->collection($resources)
            ->transformWith(new ConfigGroupTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($groupPaginator))
            ->toArray();

        return $this->success($groupDatas);
    }


    public function store(): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $validator = $this->validateForm(ValidatorInterface::RULE_CREATE);
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
        $validator = $this->validateForm(ValidatorInterface::RULE_UPDATE);

        if($validator->fails()){
            $warning = $validator->messages()->first();
            return $this->failed($warning);
        }

        $requestData = request()->all();
        try {
            DB::beginTransaction();
            $dict = Config::query()->find($id);
            $dict->update($requestData);
            DB::commit();
            return $this->success([], trans('admin.update_succeeded'));
        }catch (\Exception $exception){
            DB::commit();
            return $this->failed($exception->getMessage());
        }

    }

    public function destroy(string $id): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        //
        try {
            DB::beginTransaction();
            ConfigGroup::destroy($id);
            DB::commit();
            return $this->success();
        }catch (\Exception $exception){
            DB::rollBack();
            return $this->failed($exception->getTraceAsString());
        }

    }

}