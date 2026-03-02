<?php
namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Models\OperationLog;

class LogController extends Controller
{
	use RestfulResponse;

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