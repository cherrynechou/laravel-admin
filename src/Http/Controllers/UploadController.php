<?php

namespace CherryneChou\Admin\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use CherryneChou\Admin\Traits\HasUploadedFile;
use CherryneChou\Admin\Traits\RestfulResponse;

class UploadController extends Controller
{
	use RestfulResponse,HasUploadedFile;

    /**
     * 处理图片
     */
    public function handleImage()
    {
        try {

            $disk = $this->disk(config('upload.disk'));

            //扩展名
            $extension = request()->input('extension');

            //base64编码
            $base64_code = request()->input('base64');

            $filePath = date('Y_m_d') . '/' . Str::random (10) . '.' . $extension;

            $disk->put($filePath, base64_decode($base64_code));

            $data = [
                'fullPath'        =>  $disk->url($filePath),
                'path'              =>  $filePath,
            ];

            return $this->success($data, trans('admin.upload_succeeded'));

        }catch (\Exception $exception){

            return $this->failed($exception->getMessage());
        }

    }
}
