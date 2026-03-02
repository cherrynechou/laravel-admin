<?php
namespace CherryneChou\Admin\Transformers;

use CherryneChou\Admin\Models\LoginLog;
use Illuminate\Support\Carbon;
use League\Fractal\TransformerAbstract;
use Illuminate\Contracts\Support\Arrayable;

class LoginLogTransformer extends TransformerAbstract
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
    public function transform(LoginLog $model)
    {
        return [
            //
            'id'            =>          $model->id,
            'account'       =>          $model->account,
            'login_ip'      =>          $model->login_ip,
            'browser'       =>          $model->browser,
            'platform'      =>          $model->platform,
            'sort'          =>          $model->sort,
            'created_at'    =>          Carbon::parse($model->created_at)->toDateTimeString(),
            'updated_at'    =>          Carbon::parse($model->updated_at)->toDateTimeString(),
        ];
    }

}
