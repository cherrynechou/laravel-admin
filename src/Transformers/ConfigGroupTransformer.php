<?php
namespace CherryneChou\Admin\Transformers;

use CherryneChou\Admin\Models\ConfigGroup;
use Illuminate\Support\Carbon;
use League\Fractal\TransformerAbstract;
use Illuminate\Contracts\Support\Arrayable;

class ConfigGroupTransformer extends TransformerAbstract
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
        'configs'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(ConfigGroup $model)
    {
        return [
            //
            'id'            =>          $model->id,
            'name'          =>          $model->name,
            'key'           =>          $model->key,
            'sort'          =>          $model->sort,
            'created_at'    =>          Carbon::parse($model->created_at)->toDateTimeString(),
            'updated_at'    =>          Carbon::parse($model->updated_at)->toDateTimeString(),
        ];
    }

    /**
     * @param ConfigGroup $model
     * @return \League\Fractal\Resource\Collection
     */
    public function includeConfigs(ConfigGroup $model)
    {
        return $this->collection($model->configs, new ConfigTransformer(), '');
    }

}
