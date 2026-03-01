<?php

namespace CherryneChou\Admin\Http\Middleware;

use CherryneChou\Admin\Facades\Admin;
use CherryneChou\Admin\Support\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LogOperation
{
 	/**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        if ($this->shouldLogOperation($request)) {

            $params = $request->input();
            // 如果参数过长则不记录
            if (! empty($params)) {
                if (strlen(\json_encode($params, JSON_UNESCAPED_UNICODE)) > 5000) {
                    $params = [];
                }
            }

            $log = [
                'created_id' => Admin::user()->id,
                'created_by' => Admin::user()->name,
                'path'    => substr($request->path(), 0, 255),
                'http_method'  => $request->method(),
                'ip'      => $request->getClientIp(),
                'params'   => \json_encode($params,JSON_UNESCAPED_UNICODE),
            ];

            try {
                OperationLogModel::create($log);
            } catch (\Exception $exception) {
                // pass
            }
        }

        return $next($request);
    }


     /**
     * @param array $input
     *
     * @return string
     */
    protected function formatInput(array $input)
    {

        $secretFields = config('admin.operation_log.securt_fields');

        foreach ($secretFields as $field) {
            if ($field && ! empty($input[$field])) {
                $input[$field] = Str::limit($input[$field], 3, '******');
            }
        }

        return json_encode($input, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function shouldLogOperation(Request $request)
    {
        return config('admin.operation_log.enable')
            && !$this->inExceptArray($request)
            && $this->inAllowedMethods($request->method())
            && Admin::user();
    }

    /**
     * Whether requests using this method are allowed to be logged.
     *
     * @param string $method
     *
     * @return bool
     */
    protected function inAllowedMethods($method)
    {
        $allowedMethods = collect(config('admin.operation_log.allowed_methods'))->filter();

        if ($allowedMethods->isEmpty()) {
            return true;
        }

        return $allowedMethods->map(function ($method) {
            return strtoupper($method);
        })->contains($method);
    }

    /**
     * Determine if the request has a URI that should pass through CSRF verification.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function inExceptArray($request)
    {
        foreach (config('admin.operation_log.except') as $except) {

            if ($request->routeIs($except)) {
                return true;
            }

            $except = admin_base_path($except);


            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if (Helper::matchRequestPath($except)) {
                return true;
            }
        }

        return false;
    }
}
