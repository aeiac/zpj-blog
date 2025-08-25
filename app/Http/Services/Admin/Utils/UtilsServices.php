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
     * @return string  统一格式的响应数组（成功或失败信息）
     */
    public function generatePermission(array $params, object $adminUsersInfo): string
    {
        $data= '';
        $routes = Route::getRoutes();

        $adminId = $adminUsersInfo->id;
        $now = now();

        foreach ($routes as $route) {
            $uri = $route->uri();

            if ($uri === 'up') {
                continue;
            }

            $exists = AdminPermission::where([
                'content' => $uri,
                'status'  => AdminPermission::STATUS_ACTIVE
            ])->exists();

            if ($exists) {
                continue;
            }
            $uri = preg_replace('/{.*?}/', '', $uri);

            $uri = trim($uri, '/');
            $key = Str::replace('/', '.', $uri);

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

        if (empty($permissions)) {
            return CodeConst::getCodeMsg(CodeConst::TEMPLATE_NOT_FOUND);
        }
        $inserted = AdminPermission::insert($permissions);
        if(empty($inserted)){
            return CodeConst::getCodeMsg(CodeConst::GENERATE_FAILED);
        }
        return  $data;
    }
}
