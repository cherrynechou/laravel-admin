<?php

namespace CherryneChou\Admin\Transformers;

use App\Models\Dict;
use Illuminate\Support\Carbon;
use League\Fractal\TransformerAbstract;

class DictTransformer extends TransformerAbstract
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
    public function transform(Dict $model)
    {
        return [
            //
            'id'                    =>            $model->id,
            'name'                  =>            $model->name,
            'code'                  =>            $model->code,
            'type'                  =>            $model->type,
            'status'                =>            $model->status,
            'sort'                  =>            $model->sort,
            'remark'                =>            $model->remark,
            'created_at'            =>            Carbon::parse($model->created_at)->toDateTimeString(),
            'updated_at'            =>            Carbon::parse($model->updated_at)->toDateTimeString(),
        ];
    }
}
