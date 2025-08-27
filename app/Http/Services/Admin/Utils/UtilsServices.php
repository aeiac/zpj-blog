<?php
/**
 * @file   - UtilsServices
 * @refer  - 功能模块的逻辑层
 * -
 * @author - Mr.raycake
 * @date   - 2024-12-07 19:47:23
 */

namespace App\Http\Services\Admin\Utils;

use App\Const\Admin\CodeConst;
use App\Http\Services\Admin\BaseAdminServices;
use Illuminate\Support\Facades\Route;
use App\Models\Permission\AdminPermission;

class UtilsServices extends BaseAdminServices
{
    /**
     * 自动生成权限（包括父级路由，并设置父级ID f_id）
     *
     * @param array $params 参数（可用于未来扩展）
     * @param object $adminUsersInfo 当前管理员信息
     *
     * @return array
     */
    public function generatePermission(array $params, object $adminUsersInfo): array
    {
        $data = [];
        $permissions = [];
        $routes      = Route::getRoutes();
        $adminId     = $adminUsersInfo->id ?? 0;
        $now         = now();

        // 数据库中已有 active 权限 content
        $existingPermissions = AdminPermission::where('status', AdminPermission::$status[0])
            ->get(['id', 'content'])
            ->keyBy('content');

        foreach ($routes as $route) {
            $uri = trim(preg_replace('/{.*?}/', '', $route->uri()), '/');
            if (!$uri || $uri === 'up') {
                continue;
            }

            $uriParts = collect(explode('/', $uri))->filter()->values();
            $parentId = 0; // 顶级权限

            foreach ($uriParts as $i => $part) {
                $subUri = $uriParts->slice(0, $i + 1)->implode('/'); // 当前层级 URI
                $key = $uriParts->slice(0, $i + 1)->implode('.');     // 当前层级 key

                // 如果已存在数据库权限，获取 ID
                if (isset($existingPermissions[$subUri])) {
                    $parentId = $existingPermissions[$subUri]->id;
                    continue;
                }

                // 新权限
                $permissions[] = [
                    'name'       => $key,
                    'code'       => substr(md5(uniqid((string)mt_rand(), true)), 0, 9),
                    'content'    => $subUri,
                    'f_id'       => $parentId,
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                // 临时给 parentId，用于下一层级
                $parentId = null; // 插入后会更新 ID
            }
        }

        if (empty($permissions)) {
            $data['msg'] = CodeConst::getCodeMsg(CodeConst::TEMPLATE_NOT_FOUND);
            return $data;
        }

        // 去重 content
        $permissions = collect($permissions)->unique('content')->values()->all();

        // 插入数据库
        foreach ($permissions as $index => $perm) {
            $insertedId = AdminPermission::insertGetId($perm); // 插入单条记录，获取 ID
            $permissions[$index]['id'] = $insertedId;
        }

        // 更新 f_id：循环设置每条权限的父级 ID
        foreach ($permissions as $index => $perm) {
            if ($perm['f_id'] === null) {
                $parentUri = collect(explode('.', $perm['name']))->slice(0, -1)->implode('/');
                if ($parentUri && $parent = AdminPermission::where('content', $parentUri)->first()) {
                    AdminPermission::where('id', $perm['id'])->update(['f_id' => $parent->id]);
                }
            }
        }

        return [
            'msg'         => CodeConst::getCodeMsg(CodeConst::PERMISSION_GENERATE_SUCCESS),
            'count'       => count($permissions),
            'permissions' => $permissions,
        ];
    }


}
