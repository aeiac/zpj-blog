<?php

namespace App\Models\Systems;

use App\Models\BaseModel;

class SystemBlackList extends BaseModel
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    protected $table = 'system_blacklist';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'ip_address',
        'reason',
        'status',
    ];

}
