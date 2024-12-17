<?php

namespace App\Models\Permission;

use App\Models\BaseModel;

class AdminPermission extends BaseModel
{
    const STATUS_ACTIVE='active';
    const STATUS_INACTIVE='inactive';

    protected $table = 'blog_admin_permission';

    protected $pk = 'id';

    public $timestamps = false;


}
