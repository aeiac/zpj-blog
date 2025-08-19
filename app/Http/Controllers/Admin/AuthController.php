<?php

namespace App\Http\Controllers\Admin;

use App\Http\Services\Admin\Auth\AuthAdminServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends BaseController
{
    /**
     * 不需要身份验证的方法列表
     *
     * @var array
     */
    protected static array $excludedAuth = ['login'];

    /**
     * 管理后台管理员登录接口
     *
     * 接收用户名和密码，进行参数验证，
     * 调用服务层登录逻辑，
     * 返回登录结果（包括token等信息）。
     *
     * @param Request           $request  HTTP 请求对象
     * @param AuthAdminServices $services 依赖注入的登录服务类
     *
     * @return JsonResponse 返回 JSON 格式的响应
     */
    public function login(Request $request, AuthAdminServices $services): JsonResponse
    {
        $input = $request->post();

        $validation = Validator::make($input, [
            'name'     => 'required|string|max:10',
            'password' => 'required|string|max:16',
        ]);

        if ($validation->fails()) {
            return $this->appResponse::error(msg: $validation->errors()->first());
        }
        $userBackResult = $services->login($input);
        return $this->appResponse::success($userBackResult);
    }

    /**
     * 管理员登出接口
     *
     * 清除当前管理员用户的缓存信息，完成登出操作。
     *
     * @return JsonResponse 返回登出成功的提示信息
     */
    public function out(AuthAdminServices $services): JsonResponse
    {
        $result = $services->out($this->adminUserInfo->id);
        return $this->appResponse::success($result);
    }
}
