<?php

namespace CherryneChou\Admin\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

Trait HasRestfulResponse
{   
    /**
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse|JsonResource
     */
    public function errorNotFound($message = 'Not Found')
    {
        $this->fail($message, Response::HTTP_NOT_FOUND);
    }


    public function errorBadRequest(?string $message = 'Bad Request')
    {
        return $this->failed($message, Response::HTTP_BAD_REQUEST);
    }

    /**
     * Return a 403 forbidden error.
     *
     * @param  string  $message
     */
    public function errorForbidden(string $message = 'Forbidden')
    {
        return $this->failed($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * 内部错误 状态码 401
     *
     * @param  string  $message
     */
    public function errorInternal($message = 'Internal Error')
    {
        $this->fail($message, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * 请求未认证响应 状态码 401
     *
     * @param  string  $message
     */
    public function errorUnauthorized(string $message = 'Unauthorized')
    {
        return $this->failed($message, Response::HTTP_UNAUTHORIZED);
    }


    public function errorMethodNotAllowed($message = 'Method Not Allowed')
    {
        return $this->fail($message, Response::HTTP_METHOD_NOT_ALLOWED);
    }


    /**
     *
     * @param null $data
     * @param string $message
     * @param string $location
     * @return \Illuminate\Http\JsonResponse|JsonResource
     */
    public function accepted($data = null, string $message = 'Accepted', string $location = '')
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
    public function created($data = null, $message = 'Created', string $location = '')
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
     * @param array $data
     * @param string $message
     * @param int $status
     * @param array $headers
     * @param int $option
     * @return \Illuminate\Http\JsonResponse|JsonResource
     */
    public function success($data = [], string $message = '',int $status = Response::HTTP_OK, array $headers = [], $options = 0)
    {
        $additionalData = $this->formatData($data, $message, $status);

        if ($data instanceof JsonResource) {
            return $data->additional($additionalData);
        }

        return response()->json(array_merge($additionalData, [
            'status'    =>  $status,
            'data'      => $data,
            'success'   => true
        ]), $status, $headers, $option);
    }

    /**
     * @param string $message
     * @param int $status
     * @param array $header
     * @param int $options
     */
    public function failed(string $message = '', int $status = Response::HTTP_BAD_REQUEST, array $headers = [], int $options = 0)
    {
        $additionalData = $this->formatData(null,$message,$status);
       
        return response()->json(array_merge($additionalData,[
            'status'        =>  $status,
            'success'       => false
        ]), $status, $headers, $options);
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
        $statusText = ($status >= 400 && $status <= 499) ? 'error' : 'fail';
        $message = !$message ? ( isset(Response::$statusTexts[$status]) ? Response::$statusTexts[$status] : 'Service error') : $message;

        return [
            'statusText'    =>  $status === 200 ? 'success':  $statusText,
            'message'       =>  $message
        ];
    }

}
