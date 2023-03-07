<?php

namespace CherryneChou\Admin\Traits;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

Trait RestfulResponse
{
    /**
     * 创建
     * @param null $data
     * @param string $message
     * @return \Illuminate\Http\JsonResponse|JsonResource
     */
    public function created($data = null, $message = 'Created')
    {
        return $this->success($data, $message, Response::HTTP_CREATED);
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
     * Return a 401 unauthorized error.
     *
     * @param  string  $message
     */
    public function errorUnauthorized(string $message = '')
    {
        return $this->failed($message, 401);
    }

    /**
     * Return a 403 forbidden error.
     *
     * @param  string  $message
     */
    public function errorForbidden(string $message = '')
    {
        return $this->failed($message, 403);
    }

    /**
     * @param array $data
     * @param string $message
     * @param int $status
     * @param array $headers
     * @param int $option
     * @return \Illuminate\Http\JsonResponse|JsonResource
     */
    public function success($data = [], string $message = '',int $status = Response::HTTP_OK, array $headers = [],$option = 0)
    {
        $message = !$message ? ( isset(Response::$statusTexts[$status]) ? Response::$statusTexts[$status] : 'OK') : $message;

        $additionalData = [
            'statusText'    => 'success',
            'status'        => $status,
            'message'       => $message
        ];

        if ($data instanceof JsonResource) {
            return $data->additional($additionalData);
        }

        return response()->json(array_merge($additionalData, [
            'data' => $data ?: []
        ]), $status, $headers, $option);
    }

    /**
     * @param string $message
     * @param int $status
     * @param array $header
     * @param int $options
     */
    public function failed(string $message = '', int $status = Response::HTTP_BAD_REQUEST, array $header = [], int $options = 0)
    {
        $statusText = ($status >= 400 && $status <= 499) ? 'error' : 'fail';

        $message = !$message ? ( isset(Response::$statusTexts[$status]) ? Response::$statusTexts[$status] : 'Service error') : $message;

        return response()->json([
            'statusText'    => $statusText,
            'status'        => $status,
            'message'       => $message,// 错误描述
        ], $status,  $header,  $options);
    }
}
