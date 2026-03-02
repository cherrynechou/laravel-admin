<?php
namespace CherryneChou\Admin\Transformers;

use CherryneChou\Admin\Models\Config;
use Illuminate\Support\Carbon;
use League\Fractal\TransformerAbstract;
use Illuminate\Contracts\Support\Arrayable;

class ConfigTransformer extends TransformerAbstract
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
    public function transform(Config $model)
    {
        return [
            //
            'id'            =>          $model->id,
            'group_id'      =>          $model->group_id,
            'key'           =>          $model->key,
            'label'         =>          $model->label,
            'value'         =>          $model->value,
            'type'          =>          $model->type,
            'placeholder'   =>          $model->placeholder,
            'is_required'   =>          $model->is_required,
            'is_visible'    =>          $model->is_visible,
            'options'       =>          $model->options,
            'rules'         =>          $model->rules,
            'remark'        =>          $model->remark,
            'sort'          =>          $model->sort,
            'visible'       =>          $model->visible,
            'created_at'    =>          Carbon::parse($model->created_at)->toDateTimeString(),
            'updated_at'    =>          Carbon::parse($model->updated_at)->toDateTimeString(),
        ];
    }
}
