<?php

namespace App\Models\Systems;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static whereIn(string $string, $getRoleAndPermissionResult)
 */
class SystemAdminPermission extends Model
{
    protected $table = 'system_admin_permission';

    protected $pk = 'id';

    public $timestamps = true;


}
