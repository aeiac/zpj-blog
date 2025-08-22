<?php

namespace App\Models\Utils;

use App\Models\BaseModel;

class Link extends  BaseModel
{
    protected  $table = 'blog_link';

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

}
