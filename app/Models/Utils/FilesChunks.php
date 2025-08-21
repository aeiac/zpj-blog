<?php

namespace App\Models\Utils;

use App\Models\BaseModel;
use App\Utils\Admin\UserInfo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class FilesChunks extends BaseModel
{
    protected  $table = 'blog_files_chunks';

    protected  $primaryKey = 'id';

    protected static array $upload_status = [
        0, // 默认
        1, // 上传中
        2, // 上传成功
        3, // 上传失败
    ];

    // 允许批量新增的字段
    protected $fillable = [
        'name',
        'status',
        'type',
        'code',
        'is_deleted',
        'deleted_at',
        'created_by',
        'updated_by',
        'file_id',
        'chunk_index',
        'chunk_size',
        'path',
        'chunk_hash',
        'upload_status',
        'uploaded_at',
    ];

    /**
     * 新增一条分片记录
     *
     * @param string   $name         文件显示名称
     * @param int      $fileId       原始文件 ID
     * @param int      $chunkIndex   分片序号
     * @param int      $chunkSize    分片大小（字节）
     * @param string   $path         分片存储路径
     * @param string   $chunkHash    分片 MD5/SHA1 校验码
     * @return object  文件对象
     */
    public static function addChunk(
        string $name,
        int $fileId,
        int $chunkIndex,
        int $chunkSize,
        string $chunkHash,
        string $path,
    ): object {
        $now = Carbon::now();
        $user = UserInfo::userInfo();

        $data = [
            'name'          => $name,
            'status'        => 1,
            'type'          => 'blog',
            'code'          => (string) Str::uuid(),
            'is_deleted'    => 0,
            'created_by'    => $user->id,
            'updated_by'    => $user->id,
            'file_id'       => $fileId,
            'chunk_index'   => $chunkIndex,
            'chunk_size'    => $chunkSize,
            'path'          => $path,
            'chunk_hash'    => $chunkHash,
            'upload_status' => self::$upload_status[2],
            'uploaded_at'   => $now,
        ];
        return self::create($data);
    }

}
