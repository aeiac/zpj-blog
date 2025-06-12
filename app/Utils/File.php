<?php

namespace App\Utils;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class File
{

    /**
     * 验证上传文件的类型和大小
     *
     * @param array $allowedTypes 允许的 MIME 类型（如 ['image/jpeg', 'image/png']）
     * @param int $maxSize 允许的最大文件大小（单位：KB）
     *
     * @throws string
     */
    function validateUploadFile(object $file, array $allowedTypes = [], int $maxSize = 1024): string
    {
        $fileResult = '';
        if (!$file || !$file->isValid()) {
            $fileResult = '文件无效或未上传';
        }
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            $fileResult = '文件类型不被支持';
        }
        if ($file->getSize() / 1024 > $maxSize) {
            $units = ['B', 'KB', 'MB', 'GB'];
            $bytes = max($file->getSize(), 0);
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
            $pow = min($pow, count($units) - 1);
            $bytes /= pow(1024, $pow);
            $size = round($bytes, 2) . ' ' . $units[$pow];
            $fileResult = "文件大小不能超过 {$size}";
        }
        return $fileResult;
    }
}
