<?php

namespace App\Models\Permission;

use App\Models\BaseModel;

class AdminPermission extends BaseModel
{
    protected $table = 'blog_admin_permission';

    protected $pk = 'id';

    public $timestamps = true;


}
