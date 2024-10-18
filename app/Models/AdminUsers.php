<?php

namespace App\Models;


use App\Models\Systems\SystemAdminRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use  \Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Systems\SystemAdminUsersRole;

/**
 * @method static first()
 * @method static where()
 * @method static find(string $adminUserId)
 */
class AdminUsers extends Model
{
    protected $table = 'admin_users';
    protected $primaryKey = 'id';

    public $timestamps = true;

    public function userRoles(): HasMany
    {
        return $this->hasMany(SystemAdminUsersRole::class, 'users_id')
            ->select(['id', 'users_id', 'role_id'])
            ->with('roleDetail');
    }


}


