<?php
namespace CherryneChou\Admin\Transformers;

use CherryneChou\Admin\Models\OperationLog;
use Illuminate\Support\Carbon;
use League\Fractal\TransformerAbstract;
use Illuminate\Contracts\Support\Arrayable;

class OperationLogTransformer extends TransformerAbstract
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
    public function transform(OperationLog $model)
    {
        return [
            //
            'id'            =>          $model->id,
            'created_id'    =>          $model->created_id,
            'created_by'    =>          $model->created_by,
            'path'          =>          $model->path,
            'http_method'   =>          $model->http_method,
            'ip'            =>          $model->ip,
            'params'        =>          $model->params,
            'sort'          =>          $model->sort,
            'created_at'    =>          Carbon::parse($model->created_at)->toDateTimeString(),
            'updated_at'    =>          Carbon::parse($model->updated_at)->toDateTimeString(),
        ];
    }


}
