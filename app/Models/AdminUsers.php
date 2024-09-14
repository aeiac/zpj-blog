<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static first()
 * @method static where(string $string, mixed $name)
 * @method static find(string $adminUserId)
 */
class AdminUsers extends Model
{
    protected $table = 'admin_users';
    protected $primaryKey = 'id';
    public $timestamps = true;
}
