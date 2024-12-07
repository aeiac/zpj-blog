<?php

namespace App\Models\Permission;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class AdminUsersRole extends BaseModel
{
    protected $table = 'blog_admin_users_role';

    public $timestamps = true;

    public function roleDetail(): BelongsTo
    {
        return $this->belongsTo(AdminRole::class, 'role_id', 'id')
            ->select(['id', 'role_name']);
    }
}
