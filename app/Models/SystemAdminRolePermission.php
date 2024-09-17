<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(array $where)
 * @method static whereIn(string $string, $usersRoleResult)
 */
class SystemAdminRolePermission extends Model
{
    protected $table = "system_admin_role_permission";

    public $timestamps = true;
}
