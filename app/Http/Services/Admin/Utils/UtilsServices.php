<?php
/**
 * @file   - UtilsServices
 * @refer  - 功能模块的逻辑层
 * -
 * @author - Mr.raycake
 * @date   - 2024-12-07 19:47:23
 */

namespace App\Http\Services\Admin\Utils;

use App\Http\Services\Admin\BaseAdminServices;
use Illuminate\Support\Facades\Route;
use App\Models\Permission\AdminPermission;

class UtilsServices extends BaseAdminServices
{

    // 生成权限
    public function generatePermission(array $params, object $adminUsersInfo): array
    {
        $routes = Route::getRoutes();
        $permissions = [];
        foreach ($routes as $route) {
            if ($route->getName()) {
                $exists = AdminPermission::where([
                    'content' => $route->uri(),
                    'status' => AdminPermission::STATUS_ACTIVE
                ])->exists();
                if (!$exists) {
                    $permissions[] = [
                        'name' => $route->getName(),
                        'content' => $route->uri(),
                        'created_by' => $adminUsersInfo->id,
                        'updated_by' => $adminUsersInfo->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }
        $insertResult = AdminPermission::insert($permissions);
        if (!$insertResult) {
            return $this->appResponse::error('生权限成失败');
        }
        return $this->appResponse::success(null, '权限生成成功');
    }
}

