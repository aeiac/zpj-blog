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
use Illuminate\Support\Str;

class UtilsServices extends BaseAdminServices
{
    /**
     * 批量生成系统中未注册的权限记录
     *
     * @param array  $params         额外参数（预留，当前未使用）
     * @param object $adminUsersInfo 当前操作的管理员信息对象
     *
     * @return array  统一格式的响应数组（成功或失败信息）
     */
    public function generatePermission(array $params, object $adminUsersInfo): array
    {
        $data = [];
        $permissions = [];
        $routes      = Route::getRoutes();
        $adminId     = $adminUsersInfo->id ?? 0;
        $now         = now();

        // 获取数据库中已有 active 权限 content
        $existingUris = AdminPermission::where('status', AdminPermission::STATUS_ACTIVE)
            ->pluck('content')
            ->toArray();

        foreach ($routes as $route) {
            $uri = $route->uri();

            // 跳过不需要的路由
            if ($uri === 'up') {
                continue;
            }

            // 去掉路由参数
            $uri = preg_replace('/{.*?}/', '', $uri);
            $uri = trim($uri, '/');

            // 数据库已有权限跳过
            if (in_array($uri, $existingUris)) {
                continue;
            }

            // 转换为权限 key 格式：admin.utils.file.chunks.upload
            $key = Str::of($uri)
                ->replace('/', '.')
                ->replaceMatches('/\.\.+/', '.')
                ->trim('.')
                ->__toString();

            $permissions[] = [
                'name'       => $key,
                'code'       => substr(md5(uniqid((string)mt_rand(), true)), 0, 9),
                'content'    => $uri,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // 如果没有新权限
        if (empty($permissions)) {
            $data['msg'] = CodeConst::getCodeMsg(CodeConst::TEMPLATE_NOT_FOUND);
            return $data;
        }

        // 去重，防止同一次循环中重复
        $permissions = collect($permissions)->unique('content')->values()->all();

        // 插入数据库
        $inserted = AdminPermission::insert($permissions);
        if (!$inserted) {
            $data['msg'] = CodeConst::getCodeMsg(CodeConst::GENERATE_FAILED);
            return $data;
        }

        // 成功返回
        return [
            'msg' => CodeConst::getCodeMsg(CodeConst::PERMISSION_GENERATE_SUCCESS),
            'count' => count($permissions),
        ];
    }
}
