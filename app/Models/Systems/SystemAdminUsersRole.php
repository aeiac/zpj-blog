<?php

namespace App\Models\Systems;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Relations\BelongsTo;


class SystemAdminUsersRole extends BaseModel
{
    protected $table = 'system_admin_users_role';

    public $timestamps = true;

    public function roleDetail(): BelongsTo
    {
        return $this->belongsTo(SystemAdminRole::class, 'role_id', 'id')
            ->select(['id', 'role_name']);
    }
}
