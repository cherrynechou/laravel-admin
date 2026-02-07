<?php

namespace CherryneChou\Admin\Transformers;

use App\Models\DictData;
use Illuminate\Support\Carbon;
use League\Fractal\TransformerAbstract;

class DictDataTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        //
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [
        //
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(DictData $model)
    {
        return [
            //
            'id'                    =>            $model->id,
            'dictId'                =>            $model->dict_id,
            'code'                  =>            $model->code,
            'label'                 =>            $model->label,
            'value'                 =>            $model->value,
            'isDefault'             =>            $model->is_default,
            'status'                =>            $model->status,
            'sort'                  =>            $model->sort,
            'remark'                =>            $model->remark,
            'created_at'            =>            Carbon::parse($model->created_at)->toDateTimeString(),
            'updated_at'            =>            Carbon::parse($model->updated_at)->toDateTimeString(),
        ];
    }
}
