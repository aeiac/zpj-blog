<?php

namespace App\Models\Systems;

use Illuminate\Database\Eloquent\Model;


/**
 * @method static where(array $where)
 * @method static updateOrCreate(array $array, array $input)
 */
class SystemBlackList extends Model
{
    protected $table = 'system_blacklist';
    protected $primaryKey = 'id';

}
