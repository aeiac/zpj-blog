<?php

namespace App\Models;

use App\Models\Permission\AdminUsersRole;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminUsers extends BaseModel
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    protected $table = 'blog_admin_users';

    protected $primaryKey = 'id';

    protected $hidden = ['password','salt'];

    public $timestamps = true;

    public function userRoles(): HasMany
    {
        return $this->hasMany(AdminUsersRole::class, 'users_id')
            ->select(['id', 'users_id', 'role_id'])
            ->with('roleDetail');
    }

}


