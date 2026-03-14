<?php
namespace CherryneChou\Admin\Transformers;

use CherryneChou\Admin\Models\Department;
use Illuminate\Support\Carbon;
use League\Fractal\TransformerAbstract;
use Illuminate\Contracts\Support\Arrayable;

class DepartmentTransformer extends TransformerAbstract
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
    public function transform(Department $model)
    {
        return [
            //
            'id'                =>          $model->id,
            'parent_id'         =>          $model->parent_id,
            'name'              =>          $model->name,
            'principal'			=>          $model->principal,    //负责人 
            'email'				=>          $model->email,
            'telephone'         =>          $model->telephone,
            'status'            =>          $model->status,
            'sort'              =>          $model->sort,
            'created_at'        =>          Carbon::parse($model->created_at)->toDateTimeString(),
            'updated_at'        =>          Carbon::parse($model->updated_at)->toDateTimeString(),
        ];
    }
}