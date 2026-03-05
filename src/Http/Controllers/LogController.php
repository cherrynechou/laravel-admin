<?php
namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Models\OperationLog;
use CherryneChou\Admin\Models\LoginLog;
use CherryneChou\Admin\Transformers\OperationLogTransformer;
use CherryneChou\Admin\Transformers\LoginLogTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class LogController extends BaseController
{
    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function operationLogs(): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $opeartionLogPaginator = OperationLog::query()->orderBy('sort')->paginate();

        $resources = $opeartionLogPaginator->getCollection();

        $opeartionLogs = fractal()
                        ->collection($resources)
                        ->transformWith(new OperationLogTransformer())
                        ->paginateWith(new IlluminatePaginatorAdapter($opeartionLogPaginator))
                        ->toArray();                 

        return $this->success($opeartionLogs);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function loginLogs(): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $loginLogPaginator = LoginLog::query()->orderBy('sort')->paginate();

        $resources = $loginLogPaginator->getCollection();

        $loginLogs = fractal()
                        ->collection($resources)
                        ->transformWith(new LoginLogTransformer())
                        ->paginateWith(new IlluminatePaginatorAdapter($loginLogPaginator))
                        ->toArray();                 

        return $this->success($loginLogs);
    }
}