<?php

namespace App\Utils;

use App\Const\Admin\CodeConst;
use App\Models\Utils\Files;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class File
{

    /**
     * 验证上传文件
     *
     * 验证文件是否符合允许的 MIME 类型、扩展名及大小限制。
     *
     * @param mixed $file 文件对象（Laravel UploadedFile）
     * @param array $allowedTypes 允许的 MIME 类型，例如：
     *     图片
     *     'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp', 'image/svg+xml',
     *     文档
     *     'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
     *     'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
     *     'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
     *     'text/plain',
     *     压缩包
     *     'application/zip', 'application/x-rar-compressed', 'application/gzip', 'application/x-7z-compressed',
     *     音频
     *     'audio/mpeg', 'audio/wav',
     *     视频
     *     'video/mp4', 'video/x-msvideo', 'video/quicktime'
     * @param array $allowedExtensions 允许的扩展名，例如：
     *     'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg',
     *     'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt',
     *     'zip', 'rar', '7z', 'gz',
     *     'mp3', 'wav', 'mp4', 'avi', 'mov'
     * @param float $maxSize 允许的最大文件大小（单位：MB），默认 2.0 MB
     *
     * @return string 返回错误信息，若为空表示验证通过
     */

    public static function validateUploadFile(mixed $file, array $allowedTypes = [], array $allowedExtensions = [], float $maxSize = 2.0): string
    {
        $errors = [];

        // 文件是否有效
        if (!$file || !$file->isValid()) {
            $errors[] = CodeConst::getErrorCodeConstMessages(CodeConst::FILE_NOT_FOUND);
            return implode('; ', $errors);
        }

        // MIME 类型检查
        if (!empty($allowedTypes) && !in_array($file->getMimeType(), $allowedTypes)) {
            $errors[] = CodeConst::getErrorCodeConstMessages(CodeConst::FILE_INVALID_TYPE);
        }

        // 扩展名检查
        if (!empty($allowedExtensions)) {
            $extension = strtolower($file->getClientOriginalExtension());
            if (!in_array($extension, $allowedExtensions)) {
                $errors[] = CodeConst::getErrorCodeConstMessages(CodeConst::FILE_INVALID_TYPE);
            }
        }

        // 文件大小检查
        $fileSizeMB = $file->getSize() / 1024 / 1024;
        if ($fileSizeMB > $maxSize) {
            $errors[] = sprintf(
                CodeConst::getErrorCodeConstMessages(CodeConst::FILE_TOO_LARGE),
                self::formatFileSize($maxSize * 1024 * 1024),
                self::formatFileSize($file->getSize())
            );
        }
        return implode('; ', $errors);
    }

    /**
     * 格式化文件大小
     *
     * @param float $sizeInBytes 文件大小，单位字节
     * @return string
     */
    private static function formatFileSize(float $sizeInBytes): string
    {
        if ($sizeInBytes < 1024) return $sizeInBytes . ' B';
        $sizeInKB = $sizeInBytes / 1024;
        if ($sizeInKB < 1024) return round($sizeInKB, 2) . ' KB';
        $sizeInMB = $sizeInKB / 1024;
        if ($sizeInMB < 1024) return round($sizeInMB, 2) . ' MB';
        $sizeInGB = $sizeInMB / 1024;
        return round($sizeInGB, 2) . ' GB';
    }


    /**
     * 上传文件并保存到指定存储位置
     *
     * @param string $name        文件业务名称（如：头像、附件、合同）
     * @param mixed  $file        上传的文件对象（通常是 Illuminate\Http\UploadedFile）
     * @param string $storageType 存储方式：local=本地，oss=对象存储
     * @param string $remark      文件备注信息
     * @param string $expireAt    文件过期时间（格式：Y-m-d H:i:s）
     *
     * @return string 上传成功为''
     */
    public function uploadFile(string $name, mixed $file, string $storageType, string $remark, string $expireAt): string
    {
        $result = '';
        $datePath = date('Ymd');
        $directory = "files/$datePath";
        $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($directory, $filename, 'public');
        if ($path == 'false') {
            // 上传失败
            $result = CodeConst::getErrorCodeConstMessages(CodeConst::FILE_UPLOAD_FAILED);
        } else {
            $fPath = 'public' . Storage::url($path);
            $addFile = Files::addFile(
                $name,
                $file->getClientOriginalName(),
                $filename,
                $file->getClientOriginalExtension(),
                $fPath,
                $file->getMimeType(),
                $file->getSize(),
                $storageType,
                remark: $remark,
                expireAt: $expireAt
            );
            if (!$addFile) {
                // 存储失败
                $result = CodeConst::getErrorCodeConstMessages(CodeConst::FILE_STORAGE_FAILED);
                // 删除文件
                @unlink($fPath);
            }
        }
        return $result;
    }
}
