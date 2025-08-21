<?php

namespace App\Models\Utils;

use App\Models\BaseModel;
use App\Utils\Admin\UserInfo;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Files extends BaseModel
{
    protected  $table = 'blog_files';

    protected  $primaryKey = 'id';

    public static array $status = [
        0, // 初始化
        1, // 上传中
        2, // 上传失败
        3, // 成功
        4, // 隐藏
    ];

    /**
     * 允许批量赋值的字段
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'file_name',
        'md5_hash',
        'file_path',
        'file_type',
        'file_size',
        'upload_time',
        'file_extension',
        'is_deleted',
        'uploader_id',
        'storage_type',
        'business_tag',
        'remark',
        'expire_at',
        'download_count',
        'status',
        'created_by',
        'updated_by'
    ];

    public static array $storageType = [
        0 => 'local',  // 本地存储
        1 => 'oss'     // 远程存储
    ];

    /**
     * 插入文件记录
     *
     * @param string $name 业务名
     * @param string $fName 文件名（含扩展名）
     * @param string $fNameMd5
     * @param string $fExtension
     * @param string $fPath
     * @param string $fType
     * @param int $fSize
     * @param string $storageType 存储方式，如 local、loss
     * @param string $businessTag 业务标签
     * @param int $status 状态（0=初始化, 1=上传中, 2=失败, 3=成功）
     * @param string|null $remark 备注信息
     * @param string|null $expireAt 文件过期时间
     * @return object 新插入记录ID
     */
    public static function addFile(
        string     $name,
        string     $fName,
        string     $fNameMd5,
        string     $fExtension,
        string     $fPath,
        string     $fType,
        int        $fSize,
        string     $storageType = 'local',
        string     $businessTag = 'blog',
        int        $status = 3,
        string     $remark = null,
        string     $expireAt = null  //年-月-日-时-分-秒
    ): object
    {
        $now = Carbon::now();
        do {
            $code = (string) Str::uuid();
        } while (Files::where('code', $code)->exists());
        $user = UserInfo::userInfo();
        $data = [
            'code'           => $code,
            'name'           => $name,
            'file_name'      => $fName,
            'md5_hash'       => $fNameMd5,
            'file_path'      => $fPath,
            'file_type'      => $fType,
            'file_extension' => $fExtension,
            'file_size'      => $fSize,       // 字节
            'upload_time'    => $now,
            'storage_type'   => $storageType,
            'business_tag'   => $businessTag,
            'remark'         => $remark,
            'expire_at'      => $expireAt,
            'status'         => $status,
            'uploader_id'    => $user->id,
            'created_by'     => $user->id,
            'updated_by'     => $user->id,
        ];
        return self::create($data);
    }

    // 根据code查询文件
    public static function codeToFiles(string $code): object
    {
        return Files::where('code', $code)
            ->where('is_deleted', '0')
            ->first();
    }
}
