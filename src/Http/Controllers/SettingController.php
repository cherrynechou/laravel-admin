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
            ->parseIncludes(['configs'])
            ->toArray();

        return $this->success($ConfigResources);
    }

    public function update($group=''): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $params = request()->all();

        //需要更新数据
        $updates = $this->filterEmptyOrNullData($params);

        //数据库中的数据
        $oldDatas = Config::query()->where('group_key', $groupKey)->get();

        //只更新改的值 

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