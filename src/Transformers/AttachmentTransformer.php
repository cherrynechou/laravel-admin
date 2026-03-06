<?php
namespace CherryneChou\Admin\Transformers;

use League\Fractal\TransformerAbstract;
use CherryneChou\Admin\Models\Attachment;

class AttachmentTransformer extends TransformerAbstract
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
    public function transform(Attachment $model)
    {
    	return [
            //
            'id'            =>          $model->id,
            'sort'          =>          $model->sort,
            'remark'        =>          $model->remark,
            'created_at'    =>          Carbon::parse($model->created_at)->toDateTimeString(),
            'updated_at'    =>          Carbon::parse($model->updated_at)->toDateTimeString(),
        ];
    }
}
