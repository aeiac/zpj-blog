<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 */
class AdminUsersLog extends Model
{
    protected $table = 'admin_users_log';
    protected $primaryKey = 'id';

    protected $fillable = [
        'admin_users_id',
        'path',
        'request',
        'ip',
        'ua',
        'created_at'
    ];

    public $timestamps = false;
}
