<?php

namespace App\Models\Utils;

use App\Models\BaseModel;
use Illuminate\Support\Str;

class FilesBusiness extends BaseModel
{
    protected $table = 'blog_files_business';

    public static array $status = [
        0 => 'active', // 正常
        1 => 'inactive'// 禁用
    ];

    protected $fillable = [
        'name',           // 文件名称/显示名称
        'code',           // 唯一编码/编号
        'is_deleted',     // 逻辑删除状态
        'deleted_at',     // 逻辑删除时间
        'business_type',  // 业务类型（如: article, user, order 等）
        'business_id',    // 业务主键ID
        'file_id',        // 文件ID（关联文件表）
        'sort',           // 排序
        'created_by',     // 创建人
        'updated_by',     // 更新人
        'status',         // 状态: active=正常, inactive=禁用
        'created_at',     // 创建时间
        'updated_at',     // 更新时间
    ];

    /**
     * 为指定业务关联一个文件
     *
     * @param int $fID 文件ID
     * @param int $bId 业务主键ID
     * @param string $bType 业务类型（如: article, user, order 等）
     *
     * @return object 返回创建的 BusinessFile 模型对象
     */
    public static function addFileBusiness(int $fID, int $bId, string $bType): object
    {
        do {
            $code = (string)Str::uuid();
        } while (Files::where('code', $code)->exists());
        $data = [
            'name' => $bType . '关联文件',
            'code' => $code,
            'business_type' => $bType,
            'business_id' => $bId,
            'file_id' => $fID
        ];
        return self::create($data);
    }

    /**
     * 获取指定业务的所有关联文件
     *
     * @param int $bId 业务主键ID
     *
     * @return array 返回 BusinessFile 对象数组，每个对象包含文件信息
     */
    public static function getFiles(int $bId): array
    {
        return self::where('business_id', $bId)->where('status', self::$status[0])->get('file_id')->toArray();
    }
}
