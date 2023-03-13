<?php
namespace CherryneChou\Admin\Traits;

use Illuminate\Support\Facades\Storage;

trait HasUploadedFile
{
    /**
     * 获取文件管理仓库.
     *
     * @param  string|null  $disk
     * @return \Illuminate\Contracts\Filesystem\Filesystem|FilesystemAdapter
     */
    public function disk(string $disk = null)
    {
        return Storage::disk($disk ?: config('admin.upload.disk'));
    }
}
