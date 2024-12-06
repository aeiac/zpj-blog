<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Systems\SystemAdminUsersRole;

class AdminUsers extends BaseModel
{
    protected $table = 'admin_users';

    protected $primaryKey = 'id';

    protected $hidden = ['password'];

    public $timestamps = true;

    public function userRoles(): HasMany
    {
        return $this->hasMany(SystemAdminUsersRole::class, 'users_id')
            ->select(['id', 'users_id', 'role_id'])
            ->with('roleDetail');
    }

}


