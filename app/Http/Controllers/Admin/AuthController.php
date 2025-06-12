<?php

namespace App\Http\Controllers\Admin;

use App\Http\Services\Admin\Auth\AuthAdminServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;


class AuthController extends BaseController
{
    protected static array $excludedAuth = ['login'];

    /**
     * 后台管理员登录
     *
     * @param Request $request
     * @param AuthAdminServices $services
     *
     * @return JsonResponse
     */
    public function login(Request $request, AuthAdminServices $services): JsonResponse
    {
        $input = $request->post();
        $validation = Validator::make($input, [
            'name' => 'required|string|max:10',
            'password' => 'required|string|max:16'
        ]);
        if ($validation->fails()) {
            return $this->appResponse::error(msg: $validation->errors()->first());
        }
        $userBackResult = $services->loginAdminUser($input);
        return $this->appResponse::success($userBackResult);
    }

    /**
     * 管理员账号登出
     *
     * @return JsonResponse
     */
    public function out(): JsonResponse
    {
        $this->tokensUtils::clearAdminUserCache($this->adminUserInfo->id);
        return $this->appResponse::success(null, '账户已退出');
    }
}
