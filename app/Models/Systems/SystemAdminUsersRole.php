<?php

namespace App\Models\Systems;

use Illuminate\Database\Eloquent\Model;

use \Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static where(array $where)
 */
class SystemAdminUsersRole extends Model
{
    protected $table = 'system_admin_users_role';

    public $timestamps = true;

    public function roleDetail(): BelongsTo
    {
        return $this->belongsTo(SystemAdminRole::class, 'role_id', 'id')
            ->select(['id', 'role_name']);
    }
}
