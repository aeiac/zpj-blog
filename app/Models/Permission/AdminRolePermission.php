<?php

namespace App\Models\Permission;

use App\Models\BaseModel;

class AdminRolePermission extends BaseModel
{
    protected $table = "blog_admin_role_permission";

    public $timestamps = true;
}
