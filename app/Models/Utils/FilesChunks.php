<?php

namespace App\Models\Utils;

use App\Models\BaseModel;

class FilesChunks extends BaseModel
{
    protected  $table = 'blog_files_chunks';

    protected  $primaryKey = 'id';

    protected array $upload_status = [
        0, // 默认
        1, // 上传中
        2, // 上传成功
        3, // 上传失败
    ];

}
