<?php

namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Serializer\DataArraySerializer;
use CherryneChou\Admin\Traits\HasFilterData;
use CherryneChou\Admin\Models\ConfigGroup;
use CherryneChou\Admin\Transformers\ConfigGroupTransformer;

class ConfigController extends BaseController
{
    use HasFilterData;

    /**
     * 所有配置
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function all(): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $resources = ConfigGroup::query()->orderBy('sort')->get();

        $ConfigResources = fractal()
            ->collection($resources)
            ->transformWith(new ConfigGroupTransformer())
            ->serializeWith(new DataArraySerializer())
            ->toArray();

        return $this->success($ConfigResources);
    }

    public function update($group=''): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $params = request()->all();

        $updates = $this->filterEmptyOrNullData($params);

        try{

            DB::beginTransaction();

            DB::commit();

            return $this->success();

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->failed($exception->getMessage());
        }

    }

}