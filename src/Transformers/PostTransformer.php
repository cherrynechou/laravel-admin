<?php
namespace CherryneChou\Admin\Transformers;

use CherryneChou\Admin\Models\Post;
use Illuminate\Support\Carbon;
use League\Fractal\TransformerAbstract;
use Illuminate\Contracts\Support\Arrayable;

class PostTransformer extends TransformerAbstract
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
    public function transform(Post $model)
    {

        return [
            //
            'id'            =>          $model->id,
            'name'          =>          $model->name,
            'parent_id'     =>          $model->parent_id,
            'code'        	=>          $model->code,
            'sort'          =>          $model->sort,
            'status'		=>          $model->status,
            'remark'		=>          $model->remark,
            'created_at'    =>          Carbon::parse($model->created_at)->toDateTimeString(),
            'updated_at'    =>          Carbon::parse($model->updated_at)->toDateTimeString(),
        ];
    }
}