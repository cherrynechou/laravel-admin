<?php

namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Models\Config;
use CherryneChou\Admin\Serializer\DataArraySerializer;
use CherryneChou\Admin\Transformers\ConfigTransformer;
use CherryneChou\Admin\Contracts\ValidatorInterface;
use CherryneChou\Admin\Filters\ConfigFilter;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ConfigController extends BaseController
{
	protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'key'       => 'required',
            'label'     => 'required',
            'type'      => 'required',
        ],
        ValidatorInterface::RULE_UPDATE => [
            'key'       => 'required',
            'label'     => 'required',
            'type'      => 'required',
        ]
    ];

	public function index(ConfigFilter $filter): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $configPaginator = Config::filter($filter)->orderBy('sort')->paginate();

        $resources = $configPaginator->getCollection();

        $configDatas = fractal()
            ->collection($resources)
            ->transformWith(new ConfigTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($configPaginator))
            ->toArray();

        return $this->success($configDatas);
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
            Config::create($requestData);
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
        $resource =  Config::query()->find($id);

        $config = fractal()
            ->item($resource)
            ->transformWith(new ConfigTransformer())
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
            $config= Config::query()->find($id);
            $config->update($requestData);
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
    protected function validateForm($rule)
    {
        $message = [
            'required'   => trans('validation.attribute_not_empty')
        ];

        $attributes = [
            'key'       => trans('admin.config.label'),
            'label'     => trans('admin.config.value'),
            'type'      => trans('admin.config.type'),
        ];

        return Validator::make(request()->all(), $this->rules[$rule], $message, $attributes);
    }

    /**
     * 删除
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function destroy(string $id): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        //
        try {
            DB::beginTransaction();
            Config::destroy($id);
            DB::commit();
            return $this->success([], trans('admin.delete_succeeded'));
        }catch (\Exception $exception){
            DB::rollBack();
            return $this->failed($exception->getTraceAsString());
        }
    	
    }
}