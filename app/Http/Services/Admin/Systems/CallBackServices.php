<?php

namespace App\Http\Services\Admin\Systems;

use App\Http\Services\Admin\BaseAdminServices;

class CallBackServices  extends BaseAdminServices
{

    // 系统测试回调
    public static function test(mixed $params): string
    {
        return '';
    }

}
