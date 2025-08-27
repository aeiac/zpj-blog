<?php

namespace App\Models\Permission;

use App\Models\BaseModel;

class AdminPermission extends BaseModel
{
    protected $table = 'blog_admin_permission';

    protected $pk = 'id';

    public $timestamps = false;

    public static array $status = [
        0 => 'active',
        1 => 'inactive'
    ];

}
