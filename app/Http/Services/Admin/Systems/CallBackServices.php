<?php

namespace App\Http\Services\Admin\Systems;

use App\Const\Admin\CodeConst;
use App\Http\Services\Admin\BaseAdminServices;
use App\Models\Systems\CallBackLog;
use App\Utils\Admin\UserInfo;
use App\Utils\Log\Logs;

class CallBackServices extends BaseAdminServices
{

    // 系统测试回调
    public static function test(mixed $params): string
    {
        $reqData = UserInfo::getRequestData();
        Logs::info('SYSTEM_TEST', '博客-后台-回调日志', $params, $reqData);
        $callObj = CallBackLog::codeFirst($params['code']);
        if (empty($callObja)) {
            return CodeConst::getCodeMsg(CodeConst::DATA_INVALID);
        }
        $callObj->status = 1;
        $callObj->save();
        // 业务逻辑
        return '';
    }

}
