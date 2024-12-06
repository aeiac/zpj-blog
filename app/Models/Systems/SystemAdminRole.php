<?php

namespace App\Models\Systems;

use App\Models\BaseModel;

class SystemAdminRole extends BaseModel
{
    protected $table = 'system_admin_role';

    protected $pk = 'id';

    public $timestamps = true;

}
