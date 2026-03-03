<?php
namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Models\OperationLog;
use CherryneChou\Admin\Models\LoginLog;
use CherryneChou\Admin\Transformers\OperationLogTransformer;
use CherryneChou\Admin\Transformers\LoginLogTransformer;

class LogController extends BaseController
{
    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function operationLogs()
    {
        $opeartionLogPaginator = OperationLog::query()->orderBy('sort')->paginate();

        $resources = $opeartionLogPaginator->getCollection();

        $opeartionLogs = fractal()
                        ->collection($resources)
                        ->transformWith(new OperationLogTransformer())
                        ->serializeWith(new DataArraySerializer())
                        ->toArray();                 

        return $this->success($opeartionLogs);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function loginLogs()
    {
        $loginLogPaginator = LoginLog::query()->orderBy('sort')->paginate();

        $resources = $loginLogPaginator->getCollection();

        $loginLogs = fractal()
                        ->collection($resources)
                        ->transformWith(new LoginLogTransformer())
                        ->serializeWith(new DataArraySerializer())
                        ->toArray();                 

        return $this->success($loginLogs);
    }
}