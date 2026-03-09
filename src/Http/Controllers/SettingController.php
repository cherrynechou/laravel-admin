<?php

namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Serializer\DataArraySerializer;
use CherryneChou\Admin\Traits\HasFilterData;
use CherryneChou\Admin\Models\ConfigGroup;
use CherryneChou\Admin\Models\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Arr;
use CherryneChou\Admin\Transformers\ConfigGroupTransformer;

class SettingController extends BaseController
{
    use HasFilterData;

    /**
     * 获取网站配置
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function getWebConfig(): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        return $this->success();
    }


    /**
     * 所有配置
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function groups(): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
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


    /**
     * 更新分组配置
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function update($groupKey=''): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $params = request()->all();
        //需要更新数据
        $updates = $this->filterNullData($params);
        //数据库中的数据
        $oldConfigDatas = Config::query()->where('group_key', $groupKey)->get();

        //更改的值
        $updatedValues = [];
        foreach ($updates as $key => $value) {
            $found = $oldConfigDatas->where('key', $key)->first();
            if($found->value != $value){
                $changed['id'] = $found->id;
                $changed['value'] = $value;
                $updatedValues[] = $changed;
            }
        }

        try{
            DB::transaction(function () use ($updatedValues) {
                foreach ($updatedValues as $value) {
                    Config::query()->where('id', $value['id'])->update(Arr::except($value, ['id']));
                }
            });

            Event::dispatch("admin:config:changed");
            return $this->success();
        } catch (\Exception $exception) {
            return $this->failed($exception->getMessage());
        }

    }

    /**
     * 获取值
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function options($id): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        try {
            $config = Config::query()->findOrFail($id);
            $options = [];
            if(!is_null($config->options)){
                $options = json_decode($config->options, true);
            }
            $return['options'] = $options;
            $return['value'] = $config->value;

            return $this->success($return);
        }catch (\Exception $exception){
            return $this->failed($exception->getMessage());
        }
    }
    

    /**
     * 保存值
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function saveOptions($id): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $params = request()->all();

        try {
            DB::beginTransaction();

            $updateData = [
                'options' => json_encode($params['options']),
                'value'  => $params['value'],
            ];
            $config = Config::query()->findOrFail($id);
            $config->update($updateData);
            DB::commit();

            return $this->success();
        }catch (\Exception $exception){
            DB::rollBack();

            return $this->failed($exception->getMessage());
        }
    }

}