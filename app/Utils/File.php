<?php

namespace App\Utils;

use App\Const\Admin\CodeConst;
use App\Http\Services\Admin\BaseAdminServices;
use App\Models\Utils\Files;
use App\Models\Utils\FilesChunks;
use App\Utils\Admin\UserInfo;
use Carbon\Carbon;
use Illuminate\Support\Str;
use mysql_xdevapi\Exception;

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
     * @param string $name 文件业务名称（如：头像、附件、合同）
     * @param mixed $file 上传的文件对象（通常是 Illuminate\Http\UploadedFile）
     * @param string $storageType 存储方式：local=本地，oss=对象存储
     * @param string $remark 文件备注信息
     * @param string $expireAt 文件过期时间（格式：Y-m-d H:i:s）
     *
     * @return string 上传成功为''
     */
    public static function uploadFile(string $name, mixed $file, string $storageType, string $remark, string $expireAt): string
    {
        $result = '';
        if ($storageType == Files::$storageType[0]) {
            $datePath = date('Ymd');
            $directory = "files/$datePath";
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs($directory, $filename, 'public');
            $fPath = 'app/public/' . $path;
            if ($path == 'false') {
                // 上传失败
                $result = CodeConst::getErrorCodeConstMessages(CodeConst::FILE_UPLOAD_FAILED);
            } else {
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
                } else {
                    $result = $addFile->id;
                }
            }
        }
        if ($storageType == Files::$storageType[1]) {
            $result = '待开发';
        }

        return $result;
    }

    /**
     * 文件分片上传-初始化/发起
     *
     * 用于在服务器端创建一个新的文件上传记录，
     * 并生成唯一的文件编码，用于后续分片上传。
     *
     * @return array 返回初始化结果，包括唯一文件编码、上传状态等信息
     */
    public static function fileChunksStart(string $fileName, string $storageType): array
    {
        $data = [];
        $file = Files::addFile(
            name: $fileName, fName: 'null', fNameMd5: 'null', fExtension: 0, fPath: 0, fType: 0, fSize: 0, storageType: $storageType, status: Files::$status[1]
        );
        $data['file_code'] = $file->code;
        return $data;
    }

    /**
     * 文件分片上传
     *
     * 接收单个文件分片并存储，同时记录分片信息。
     *
     * @param string $fCode 文件唯一编码，用于标识原始文件
     * @param mixed $file 分片文件对象，通常为上传的文件实例
     * @param int $chunkIndex 分片序号，起始值为 0
     *
     * @return string 返回上传结果信息，成功或失败的提示
     *
     * @throws \Exception 可能抛出文件存储或数据库异常
     */
    public static function fileChunksUpload(string $fCode, mixed $file, int $chunkIndex): string
    {
        $result = '';
        $atFile = Files::codeToFiles($fCode);

        // 验证是否存储存在
        if (empty($atFile)) {
            return CodeConst::getErrorCodeConstMessages(CodeConst::DATA_NOT_FOUND);
        }

        // 限制重复提交
        if ($atFile->status == Files::$status[3]) {
            return CodeConst::getErrorCodeConstMessages(CodeConst::DATA_DUPLICATE);
        }

        // 限制重复上传分片
        $atFileChunk = FilesChunks::where('file_id', $atFile->id)->get('chunk_index')->toArray();
        $chunkIndexS = array_column($atFileChunk, 'chunk_index');
        if (in_array($chunkIndex, $chunkIndexS)) {
            return sprintf(
                CodeConst::getErrorCodeConstMessages(CodeConst::FILE_SAVE_FAILED),
                '[' . implode('|', $chunkIndexS) . ']'
            );
        }

        // 区分存储位置
        if ($atFile->storage_type == Files::$storageType[0]) {
            // 存储
            $datePath = date('Ymd');
            $directory = "files/$datePath";
            $filename = Str::random(20);
            $path = $file->storeAs($directory, $filename, 'public');
            $fPath = 'app/public/' . $path;
            try {
                if ($path == 'false') {
                    // 上传失败
                    $result = CodeConst::getErrorCodeConstMessages(CodeConst::FILE_UPLOAD_FAILED);
                } else {
                    $addResult = FilesChunks::addChunk(
                        name: $file->getClientOriginalName(),
                        fileId: $atFile->id,
                        chunkIndex: $chunkIndex,
                        chunkSize: $file->getSize(),
                        chunkHash: md5_file(storage_path($fPath)),
                        path: $fPath,
                    );
                    if (!$addResult) {
                        // 存储失败
                        $result = CodeConst::getErrorCodeConstMessages(CodeConst::FILE_STORAGE_FAILED);
                        // 删除文件
                        @unlink($fPath);
                    } else {
                        // 存储文件成功
                        $result = (string)$addResult->id;
                    }
                }
            } catch (Exception) {
                // 删除文件
                @unlink($fPath);
            }
        }

        // 远程服务器存储
        if ($atFile->storage_type == Files::$storageType[1]) {
            $result = 'oss待开发';
        }

        return $result;
    }

    /**
     * 文件分片融合
     *
     * 将指定文件编码（$fCode）对应的所有分片按顺序合并成完整文件。
     * 融合完成后，会更新文件状态为“已完成”，并可以获取融合后的文件基础信息。
     *
     * @param string $fCode 文件唯一编码，用于标识整个文件
     * @param int $fCount 预期的分片总数，用于校验是否所有分片已上传
     *
     * @return string 融合结果状态或错误消息
     */
    public static function fileChunkMerge(string $fCode, int $fCount): string
    {
        $result = '';
        $atFile = Files::codeToFiles($fCode);

        // 验证是否存储存在
        if (empty($atFile)) {
            return CodeConst::getErrorCodeConstMessages(CodeConst::DATA_NOT_FOUND);
        }

        // 验证是否已融合
        if ($atFile->status == Files::$status[3]) {
            return CodeConst::getErrorCodeConstMessages(CodeConst::DATA_DUPLICATE);
        }

        // 校验分片数量是否达标
        $chunkS = FilesChunks::where('file_id', $atFile->id)
            ->orderBy('chunk_index')
            ->get()
            ->toArray();
        if ($fCount != count($chunkS)) {
            return CodeConst::getErrorCodeConstMessages(CodeConst::FILE_COUNT_FAILED);
        }

        // 区分存储位置并融合文件
        $targetFile = '';
        if ($atFile->storage_type == Files::$storageType[0]) {
            $directory = storage_path('app/public/files/');
            $datePath = date('Ymd');
            $targetFile = $directory . $datePath . '/' . $atFile->file_name;
            $out = fopen($targetFile, 'wb');
            foreach ($chunkS as $chunk) {
                $chunkPath = storage_path($chunk['path']);
                if (!file_exists($chunkPath)) {
                    fclose($out);
                    return CodeConst::getErrorCodeConstMessages(CodeConst::FILE_MISSING_FAILED);
                }
                $in = fopen($chunkPath, 'rb');
                while (!feof($in)) {
                    fwrite($out, fread($in, 10192)); // 每次读 10KB
                }
                fclose($in);
            }
        }
        if ($atFile->storage_type == Files::$storageType[1]) {
            $result = 'oss待开发';
        }

        // 校验文件位置
        if (file_exists($targetFile) && is_file($targetFile)) {
            // 修改基础数据
            $atFile->md5_hash = md5_file($targetFile);
            $atFile->file_path = $targetFile;
            $atFile->file_type = mime_content_type($targetFile);
            $atFile->file_extension = pathinfo($targetFile, PATHINFO_EXTENSION);
            $atFile->file_size = filesize($targetFile);
            $atFile->status = Files::$status[3];
            $atFile->updated_at = now();
            if (!$atFile->save()) {
                $result = CodeConst::getErrorCodeConstMessages(CodeConst::DATA_UPDATE_FAILED);
                // 删除文件
                @unlink($targetFile);
            }

        } else {
            return CodeConst::getErrorCodeConstMessages(CodeConst::FILE_MISSING_FAILED);
        }

        return $result;
    }


    /**
     * 文件分片上传 - 断点续传
     *
     * 查询指定文件已上传的分片信息，用于实现断点续传。
     * 客户端可以根据返回的分片信息，决定从哪个分片继续上传。
     *
     * @param string $fCode 唯一文件编码，用于标识整个文件
     *
     * @return array 返回结果示例：
     * [
     *     'existed' => true|false,            // 是否存在已上传的分片
     *     'fragment_index' => int,            // 已上传分片中最大序号（从0开始），-1 表示未上传任何分片
     *      'fragment_list'=> []               // 已上传分片数据集
     *     'fragment_index_bytes' => string    // 已上传分片累计大小，单位 KB
     * ]
     */
    public static function fileChunksResume(string $fCode): array
    {
        $result = [];
        $result['existed'] = false;
        $result['fragment_index'] = -1;
        $result['fragment_index_bytes'] = '0 KB';
        $result['fragment_list'] = [];

        $atFile = Files::codeToFiles($fCode);

        if (empty($atFile)) {
            $result['msg'] = CodeConst::getErrorCodeConstMessages(CodeConst::DATA_NOT_FOUND);
        }
        if ($atFile->status == Files::$status[3]) {
            $result['msg'] = CodeConst::getErrorCodeConstMessages(CodeConst::FILE_SUCCESS_FAILED);
        } else {
            // 查询分片信息
            $chunkS = FilesChunks::where('file_id', $atFile->id)
                ->orderBy('chunk_index')
                ->get(['chunk_index', 'chunk_size'])
                ->toArray();
            if (!empty($chunkS)) {
                $chunkIndexS = array_column($chunkS, 'chunk_index');
                $totalBytes = array_sum(array_column($chunkS, 'chunk_size'));
                $result['existed'] = true;
                $result['fragment_index'] = max($chunkIndexS);
                $result['fragment_list'] = '[' . implode('|', $chunkIndexS) . ']';
                $result['fragment_index_bytes'] = round($totalBytes / 1024, 2) . ' KB';
                $result['msg'] = '';
            }
        }
        return $result;
    }

    /**
     * 查询文件列表（支持分页和多条件筛选）
     *
     * @param array $params 查询参数数组，包括：
     *      - 'file_name'    => (string|null) 文件名称模糊搜索
     *      - 'storage'      => (string|null) 存储类型，可选 local、oss 等
     *      - 'file_type'    => (string|null) 文件类型/扩展名，例如 pdf、doc
     *      - 'uploader_id'  => (int|null) 上传人 ID
     *      - 'status'       => (int|null) 文件状态，参考 Files::$status
     *      - 'date_from'    => (string|null) 起始上传时间，格式 YYYY-MM-DD
     *      - 'date_to'      => (string|null) 结束上传时间，格式 YYYY-MM-DD
     *      - 'page'         => (int|null) 当前页码，默认 1
     *      - 'per_page'     => (int|null) 每页条数，默认 10
     *
     * @return array 返回分页后的文件列表数据，包括：
     *      - 'data'          => 文件记录数组（只包含指定字段）
     *      - 'current_page'  => 当前页码
     *      - 'per_page'      => 每页条数
     *      - 'total'         => 总记录数
     *      - 'last_page'     => 总页数
     * @noinspection SpellCheckingInspection
     */
    public static function queryFileList(array $params): array
    {
        $data = [];
        $where = [];
        if (!empty($params['file_name'])) {
            $where[] = ['file_name', 'like', '%' . $params['file_name'] . '%'];
        }
        if (!empty($params['storage'])) {
            $where[] = ['storage_type', '=', $params['storage']];
        }
        if (!empty($params['file_type'])) {
            $where[] = ['file_type', '=', $params['file_type']];
        }
        if (!empty($params['uploader_id'])) {
            $where[] = ['uploader_id', '=', $params['uploader_id']];
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $where[] = ['status', '=', $params['status']];
        }
        if (!empty($params['date_from'])) {
            $where[] = ['upload_time', '>=', $params['date_from']];
        }
        if (!empty($params['date_to'])) {
            $where[] = ['upload_time', '<=', $params['date_to']];
        }

        // 分页参数
        $perPage = isset($input['per_page']) && is_numeric($input['per_page']) ? (int)$input['per_page'] : 10;

        // 查询并分页
        $query = Files::where($where)
            ->orderByDesc('created_at');
        $paginatedData = $query->paginate($perPage)->toArray();
        $data['files_info'] = BaseAdminServices::paginateToArray($paginatedData);
        return $data;
    }

    /**
     * 更新文件记录
     *
     * @param int $fileId 要更新的文件ID
     * @param array $params 要更新的字段
     *
     * @return string 返回 true 表示成功，返回错误信息表示失败
     */
    public static function fileOperate(int $fileId, array $params): string
    {
        $data = '';
        $now = Carbon::now();
        $user = UserInfo::userInfo();
        $file = Files::find($fileId);
        if (empty($file)) {
            return CodeConst::getErrorCodeConstMessages(CodeConst::FILE_MISSING_FAILED);
        }
        if (!empty($params['file_name'])) {
            $params['file_name'] = $params['file_name'] . '.' . $file->file_extension;
        }
        if (!empty($params['is_deleted'])) {
            $params['deleted_at'] = $now;
        }
        $params['updated_by'] = $user->id;
        $file->fill($params);
        if (!$file->save()) {
            return CodeConst::getErrorCodeConstMessages(CodeConst::DATA_UPDATE_FAILED);
        }
        return $data;
    }

}
