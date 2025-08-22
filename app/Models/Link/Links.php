<?php

namespace App\Models\Link;

use App\Models\BaseModel;

class Links extends  BaseModel
{
    protected  $table = 'blog_links';

    protected  $primaryKey = 'id';

    public static array $status = [
        0, // 默认
        1, // 启用
        2, // 禁用
    ];

    public static array $type = [
        1, // 业务链接
        2, // 友链
        3, // 链接
    ];

    protected $fillable = [
        'name',
        'url',
        'type',
        'status',
        'is_deleted',
        'created_by',
        'updated_by',
    ];

    /**
     * 添加一个新的链接记录到 blog_links 表
     *
     * @param string $name   链接名称/显示名称
     * @param int    $status 状态：0=默认,1=启用,2=禁用
     * @param int    $type   类型：1=业务链接、2=友链、3=链接（默认=3）
     * @param string $url    链接地址（必须是合法 URL）
     *
     * @return object 返回新建的链接对象（Eloquent 模型实例）
     */
    public  function addLinks(string $name, string $url, int $type = 3): object
    {
        $data = [
            'name'       => $name,
            'type'       => $type,
            'url'        => $url,
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ];
        return self::create($data);
    }

}
