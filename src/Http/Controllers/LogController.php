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
       $opeartionLogPaginator = LoginLog::query()->orderBy('sort')->paginate();

        $resources = $opeartionLogPaginator->getCollection();

        $opeartionLogs = fractal()
                        ->collection($resources)
                        ->transformWith(new LoginLogTransformer())
                        ->serializeWith(new DataArraySerializer())
                        ->toArray();                 

        return $this->success($opeartionLogs);
    }
}