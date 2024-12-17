<?php
/**
 * @file   - UtilsController
 * @refer  - 作用于编写后台系统功能模块。
 * -
 * @author - Mr.raycake
 * @date   - 2024-12-07 17:51:29
 */

namespace App\Http\Controllers\Admin;

use App\Http\Services\Admin\Utils\UtilsServices;
use Illuminate\Http\Request;

class UtilsController extends BaseController
{

    /**
     * 生成权限
     *
     * @param Request $request
     * @param UtilsServices $services
     *
     * @return array
     */
    public function permission(Request $request, UtilsServices $services): array
    {
        $params=$request->all();
        $result=$services->generatePermission($params,$this->adminUserInfo);
        return $result;
    }

}
