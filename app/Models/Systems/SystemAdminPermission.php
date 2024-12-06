<?php

namespace App\Models\Systems;

use App\Models\BaseModel;

class SystemAdminPermission extends BaseModel
{
    protected $table = 'system_admin_permission';

    protected $pk = 'id';

    public $timestamps = true;


}
