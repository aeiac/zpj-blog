<?php

namespace App\Models\Permission;

use App\Models\BaseModel;

class AdminRole extends BaseModel
{
    protected $table = 'blog_admin_role';

    protected $pk = 'id';

    public $timestamps = true;

    public static array $status = [
        0 => 'active',
        1 => 'inactive'
    ];

}
