<?php

namespace App\Http\Services\Admin\Permission;

use App\Http\Services\Admin\BaseAdminServices;
use App\Models\AdminUsers;
use App\Models\Permission\AdminPermission;
use App\Models\Permission\AdminRole;
use App\Models\Permission\AdminRolePermission;
use App\Models\Permission\AdminUsersRole;

class PermissionServices extends BaseAdminServices
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
        $data = [];
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
        $data['admin_user_info'] = $this->paginateToArray(
            AdminUsers::query()
                ->with('userRoles')
                ->where($where)
                ->orderBy('id', 'desc')
                ->paginate((int)$input['per_page'] ?: 10, ['*'])
                ->toArray()
        );
        return $data;
    }


    /**
     * 获取角色列表
     *
     * 支持按角色名称和状态进行筛选，并返回分页结果。
     *
     * @param array $params {
     *     @type string|null $role_name 角色名称关键字（模糊搜索）
     *     @type string|int|null $status 角色状态
     *     @type int $per_page           每页数量，默认 10
     * }
     *
     * @return array
     */
    public function rolesList(array $params): array
    {
        $data = [];
        $query = AdminRole::query();
        if (isset($params['role_name']) && $params['role_name'] !== '') {
            $query->where(['role_name', 'like', '%' . $params['role_name'] . '%']);
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', $params['status']);
        }

        // 分页参数
        $perPage = isset($params['per_page']) && is_numeric($params['per_page']) ? (int)$params['per_page'] : 10;

        // 查询并分页
        $query = $query->orderByDesc('created_at')->paginate($perPage)->toArray();
        $data['info'] = BaseAdminServices::paginateToArray($query);
        return $data;

    }

    /**
     * 获取权限列表
     *
     * 支持按内容、状态和是否为顶级权限进行筛选，并返回分页结果。
     *
     * @param array $params {
     *     @type string|null $content   权限内容关键字（模糊搜索）
     *     @type string|int|null $status 权限状态
     *     @type bool $is_father         是否仅查询顶级权限（f_id 为空）
     *     @type int $per_page           每页数量，默认 10
     * }
     *
     * @return array
     */
    public function permissionList(array $params): array
    {
        $data = [];
        $query = AdminPermission::query();

        if (isset($params['content']) && $params['content'] !== '') {
            $query->where(['content', 'like', '%' . $params['content'] . '%']);
        }

        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', $params['status']);
        }

        if ($params['is_father']) {
            $query->whereNull('f_id');
        }

        // 分页参数
        $perPage = isset($params['per_page']) && is_numeric($params['per_page']) ? (int)$params['per_page'] : 10;

        // 查询并分页
        $query = $query->orderByDesc('created_at')->paginate($perPage)->toArray();
        $data['info'] = BaseAdminServices::paginateToArray($query);
        return $data;
    }

}
