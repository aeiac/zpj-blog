<?php

namespace App\Http\Controllers\Admin;

use App\Http\Services\Admin\Permission\PermissionServices;
use App\Models\Permission\AdminPermission;
use App\Models\Permission\AdminRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends BaseController
{

    /**
     * 用户列表
     *
     * @param Request $request
     * @param PermissionServices $services
     *
     * @return array
     */
    public function admins(Request $request, PermissionServices $services): array
    {
        $input = $request->all();
        $data = $services->getSelectAdminUsersList($input);
        return $this->appResponse::successToArray($data);

    }

    /**
     * 获取角色列表
     *
     * 支持按内容、状态和是否为顶级权限进行筛选，并返回分页结果。
     *
     * @param Request $request
     * @param PermissionServices $services
     * @return array
     */
    public function roles(Request $request, PermissionServices $services): array
    {
        $params = $request->all();
        $validation = Validator::make($params, [
            'role_name'     => 'required|integer',
            'status'        => 'required|string|in:'.implode(',',AdminRole::$status),
            'page'          => 'nullable|integer|min:1',
            'per_page'      => 'nullable|integer|min:1'
        ]);
        if ($validation->fails()) {
            return $this->appResponse::errorToArray(msg: $validation->errors()->first());
        }
        $result = $services->rolesList($params);
        return $this->appResponse::successToArray($result);
    }


    /**
     * 获取权限列表
     *
     * 支持按内容、状态和是否为顶级权限进行筛选，并返回分页结果。
     *
     * @param Request $request
     * @param PermissionServices $services
     * @return array
     */
    public function permissionList(Request $request, PermissionServices $services): array
    {
        $params = $request->all();
        $validation = Validator::make($params, [
            'content'     => 'nullable|string',
            'status'      => 'nullable|string|in:'.implode(',',AdminPermission::$status),
            'is_father'   => 'nullable|boole',
            'f_id'        => 'nullable|integer|min:1',
            'page'        => 'nullable|integer|min:1',
            'per_page'    => 'nullable|integer|min:1'
        ]);
        if ($validation->fails()) {
            return $this->appResponse::errorToArray(msg: $validation->errors()->first());
        }
        $result = $services->permissionList($params);
        return $this->appResponse::successToArray($result);
    }


}
