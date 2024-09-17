<?php

namespace App\Http\Services\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Systems\SystemAdminPermission;
use App\Models\Systems\SystemAdminRolePermission;
use App\Models\Systems\SystemAdminUsersRole;

class SystemAdminPermissionAuthServices extends AdminController
{
    /**
     * 查询用户角色权限
     * @param $userInfo // 基础用户信息
     * @param $routerPath // 路由路径
     * @return bool
     */
    public static function getSelectRoleAndPermissionInner($userInfo, string $routerPath): bool
    {
        $where = [];
        if (isset($userInfo->id) && $userInfo->id !== '') {
            $where[] = ['users_id', '=', $userInfo->id];
        }
        $usersRoleResult = SystemAdminUsersRole::where($where)
            ->pluck('role_id')
            ->toArray();
        $getRoleAndPermissionResult = SystemAdminRolePermission::whereIn('role_id', $usersRoleResult)
            ->pluck('permission_id')
            ->toArray();
        $getAndPermissionResult = SystemAdminPermission::whereIn('id', $getRoleAndPermissionResult)
            ->pluck('content')
            ->toArray();
        return !in_array($routerPath, $getAndPermissionResult);
    }

}
