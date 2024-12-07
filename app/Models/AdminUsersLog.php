<?php

namespace App\Models;

class AdminUsersLog extends BaseModel
{
    protected $table = 'blog_admin_users_log';
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
