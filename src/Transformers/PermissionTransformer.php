<?php
namespace CherryneChou\Admin\Transformers;

use CherryneChou\Admin\Models\Permission;
use Illuminate\Support\Carbon;
use League\Fractal\TransformerAbstract;
use Illuminate\Contracts\Support\Arrayable;

class PermissionTransformer extends TransformerAbstract
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
    public function transform(Permission $model)
    {
        $method = $model->http_method ?: [];
        $path = array_filter($model->http_path);

        $method = collect($method ?: ['ANY'])->unique()->map(function($name){
            return strtoupper($name);
        });

        $max = 3 ;
        if(count($path) > $max){
            $path = array_slice($path,0, $max);
            array_push($path,'...');
        }

        $path = collect($path)->map(function($path) use (&$method){
            return $path;
        });

        return [
            //
            'id'            =>          $model->id,
            'name'          =>          $model->name,
            'slug'          =>          $model->slug,
            'methods'       =>          $model->parent_id > 0 ? $method : [],
            'paths'         =>          $path,
            'order'         =>          $model->order,
            'parent_id'     =>          $model->parent_id,
            'created_at'    =>          Carbon::parse($model->created_at)->toDateTimeString(),
            'updated_at'    =>          Carbon::parse($model->updated_at)->toDateTimeString(),
        ];
    }
}