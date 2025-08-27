<?php

namespace App\Models\Articles;

use App\Models\BaseModel;

class Articlesr extends BaseModel
{
    protected $table = 'blog_articles';
    protected $primaryKey = 'id';

    public $timestamps = true;

    public static array $status = [
        'draft',       // 草稿
        'published',   // 已发布
        'archived',    // 存档
        'disabled'     // 禁用
    ];


}
