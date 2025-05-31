<?php

namespace App\Http\Controllers\Admin;

use App\Http\Services\Admin\Permission\PermissionServices;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class PermissionController extends BaseController
{

    /**
     * 权限管理-用户列表
     *
     * @param Request $request
     * @param PermissionServices $services
     *
     * @return JsonResponse
     */
    public function admins(Request $request, PermissionServices $services): JsonResponse
    {
        $input = $request->all();
        $paginateResult = $services->getSelectAdminUsersList($input);
        return $this->appResponse::success($this->paginateToArray($paginateResult));
    }
    // 角色列表-- 添加角色-- 修改-- 配置权限--

    // 权限列表-- 添加权限-- 修改权限--


}
