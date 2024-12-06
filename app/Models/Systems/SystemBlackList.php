<?php

namespace App\Models\Systems;

use Illuminate\Database\Eloquent\Model;


/**
 * @method static where()
 * @method static updateOrCreate(array $array, array $input)
 */
class SystemBlackList extends Model
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
