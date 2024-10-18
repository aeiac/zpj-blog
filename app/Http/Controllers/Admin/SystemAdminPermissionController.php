<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Services\Admin\Systems\SystemAdminPermissionAuthServices;
use Illuminate\Http\Request;

class SystemAdminPermissionController extends AdminController
{

    /**
     * 权限管理-用户列表
     * @param Request $request
     * @param SystemAdminPermissionAuthServices $services
     * @return array
     */
    public function getSelectAdminUsersList(Request $request, SystemAdminPermissionAuthServices $services): array
    {
        $input = $request->all();
        $paginateResult = $services->getSelectAdminUsersList($input);
        return $this->success($this->paginateToArray($paginateResult));
    }
    // 角色列表-- 添加角色-- 修改-- 配置权限--

    // 权限列表-- 添加权限-- 修改权限--


}
