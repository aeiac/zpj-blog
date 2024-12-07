<?php

namespace App\Http\Services\Admin\Permission;

use App\Models\AdminUsers;
use App\Models\Permission\AdminPermission;
use App\Models\Permission\AdminRolePermission;
use App\Models\Permission\AdminUsersRole;

class PermissionServices
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
        $usersRoleResult = AdminUsersRole::where($where)
            ->pluck('role_id')
            ->toArray();
        $getRoleAndPermissionResult = AdminRolePermission::whereIn('role_id', $usersRoleResult)
            ->pluck('permission_id')
            ->toArray();
        $getAndPermissionResult = AdminPermission::whereIn('id', $getRoleAndPermissionResult)
            ->pluck('content')
            ->toArray();
        return !in_array($routerPath, $getAndPermissionResult);
    }

    /**
     * 管理员列表
     * @param array $input
     * @return array
     */
    public function getSelectAdminUsersList(array $input): array
    {
        $where = [];
        if (isset($input['id']) && $input['id'] !== '') {
            $where[] = ['id', '=', $input['id']];
        }
        if (isset($input['name']) && $input['name'] !== '') {
            $where[] = ['name', 'like', '%' . $input['name'] . '%'];
        }
        if (isset($input['nickname']) && $input['nickname'] !== '') {
            $where[] = ['nickname', 'like', '%' . $input['nickname'] . '%'];
        }
        if (isset($input['status']) && $input['status'] !== '') {
            $where[] = ['status', '=', $input['status']];
        } else {
            $where[] = ['status', '=', 1];
        }
        if (isset($input['start_time']) && $input['start_time'] !== '') {
            $where[] = ['created_at', '>=', $input['start_time']];
        }
        if (isset($input['end_time']) && $input['end_time'] !== '') {
            $where[] = ['created_at', '<=', $input['end_time']];
        }
        return AdminUsers::query()
            ->with('userRoles')
            ->where($where)
            ->orderBy('id', 'desc')
            ->paginate((int)$input['per_page'] ?: 10, ['*'])
            ->toArray();
    }

}
