<?php

namespace CherryneChou\Admin\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use CherryneChou\Admin\Traits\HasUploadedFile;

class UploadController extends Controller
{
	use RestfulResponse,HasUploadedFile;

    /**
     * 处理图片
     */
    public function handleImage()
    {
        try {

            $disk = $this->disk(config('easycms.disk'));

            $extension = request()->input('extension');

            $fileData = request()->input('fileData');

            $filePath = date('Y_m_d') . '/' . Str::random (10) . '.' . $extension;

            $disk->put($filePath, base64_decode($fileData));

            $data = [
                'remotePath'        =>  $disk->url($filePath),
                'path'              =>  $filePath,
            ];

            return $this->success($data, trans('admin.upload_succeeded'));

        }catch (\Exception $exception){

            return $this->failed($exception->getMessage());
        }

    }
}
