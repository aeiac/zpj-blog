<?php

namespace App\Models\Systems;

use App\Models\BaseModel;

class CallBackLog extends BaseModel
{
    protected $table = 'blog_admin_callback_log';

    protected $primaryKey = 'id';

    public $timestamps = true;
    protected $fillable = [
        'name',
        'code',
        'type',
        'status',
        'is_deleted',
        'env',
        'third_party',
        'request_url',
        'request_method',
        'request_payload',
        'http_status',
        'res_status',
        'res_data',
        'is_success',
        'error_message',
        'retry_count',
        'source_ip',
        'deleted_at',
    ];

    public static function add(array $data) :object
    {
        return self::create($data);
    }

}
