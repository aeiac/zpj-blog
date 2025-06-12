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
use Illuminate\Support\Str;

class UtilsServices extends BaseAdminServices
{

    // 生成权限
    public function generatePermission(array $params, object $adminUsersInfo): array
    {
        $data = [];
        $routes = Route::getRoutes();
        $permissions = [];
        foreach ($routes as $route) {
            if ($route->uri() !== 'up') {
                $exists = AdminPermission::where([
                    'content' => $route->uri(),
                    'status' => AdminPermission::STATUS_ACTIVE
                ])->exists();
                if (!$exists) {
                    $permissions[] = [
                        'name' => str_replace('/', '.',$route->uri() ),
                        'code' => substr(md5(uniqid((string)mt_rand(), true)), 0, 9),
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
            return  $this->appResponse::errorToArray(msg:'权限生成失败');
        }
        return $this->appResponse::successToArray(msg: '权限生成成功');
    }
}

