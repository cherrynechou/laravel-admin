<?php

namespace CherryneChou\Admin\Traits;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

Trait RestfulResponse
{

    /**
     *  请求已接受响应 状态码 202
     *
     * @param  null  $data
     * @param  string  $message
     * @param  string  $location
     * @return JsonResponse|JsonResource
     */
    public function accepted($data = null, string $message = '', string $location = '')
    {
        $response = $this->success($data, $message, Response::HTTP_ACCEPTED);
        if ($location) {
            $response->header('Location', $location);
        }

        return $response;
    }


    /**
     * 创建
     * @param null $data
     * @param string $message
     * @return \Illuminate\Http\JsonResponse|JsonResource
     */
    public function created($data = null, $message = '', string $location = '')
    {
        $response =  $this->success($data, $message, Response::HTTP_CREATED);

        if ($location) {
            $response->header('Location', $location);
        }

        return $response;
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse|JsonResource
     */
    public function noContent($message = 'No content')
    {
        return $this->success(null, $message, Response::HTTP_NO_CONTENT);
    }

    /**
     * 错误请求响应 状态码 400
     *
     * @param  string|null  $message
     */
    public function errorBadRequest(?string $message = ''): void
    {
        return $this->fail($message, Response::HTTP_BAD_REQUEST);
    }

    /**
     * 请求未认证响应 状态码 401
     *
     * @param  string  $message
     */
    public function errorUnauthorized(string $message = ''): void
    {
        return $this->fail($message, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Return a 401 unauthorized error.
     *
     * @param  string  $message
     */
    public function errorUnauthorized(string $message = '')
    {
        return $this->failed($message, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Return a 403 forbidden error.
     *
     * @param  string  $message
     */
    public function errorForbidden(string $message = '')
    {
        return $this->failed($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * @param array $data
     * @param string $message
     * @param int $status
     * @param array $headers
     * @param int $option
     * @return \Illuminate\Http\JsonResponse|JsonResource
     */
    public function success($data = [], string $message = '',int $status = Response::HTTP_OK, array $headers = [], $option = 0)
    {
         return $this->response($this->formatData($data, $message, $status), $status, $headers, $option);
    }

    /**
     * @param string $message
     * @param int $status
     * @param array $header
     * @param int $options
     */
    public function failed(string $message = '', int $status = Response::HTTP_BAD_REQUEST, array $header = [], int $options = 0)
    {
        return  $this->response($this->formatData(null, $message, $status), $status, $header, $options);
    }


     /**
     * 格式化数据
     *
     * @param  JsonResource|array|null  $data
     * @param  $message
     * @param  $code
     * @return array
     */
    protected function formatData($data, $message, $status): array
    {
        $originalCode = $code;
        $status = (int) substr($status, 0, 3); // notice
        if ($status >= 400 && $status <= 499) {// client error
            $statusText = 'error';
        } elseif ($code >= 500 && $code <= 599) {// service error
            $statusText = 'fail';
        } else {
            $statusText = 'success';
        }

        $message = !$message ? ( isset(Response::$statusTexts[$status]) ? Response::$statusTexts[$status] : 'Service error') : $message;

        return [
            'statusText'    => $statusText,
            'status'        => $status,
            'message'       => $message,
            'data'          => $data ?: (object) $data,
        ];
    }


    /**
     * 基本响应
     *
     * @param  mixed  $data
     * @param  int  $status
     * @param  array  $headers
     * @param  int  $options
     * @return JsonResponse
     */
    protected function response($data = [], $status = Response::HTTP_OK, array $headers = [], $options = 0): JsonResponse
    {
        return new JsonResponse($data, $status, $headers, $options);
    }
}
