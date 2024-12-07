<?php

namespace App\Http\Controllers\Admin;

use App\Http\Services\Admin\Auth\AuthAdminServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;



class AuthController extends BaseController
{
    protected static array $excludedAuth = ['login'];

    /**
     * 后台管理员登录
     *
     * @param Request $request
     * @param AuthAdminServices $services
     *
     * @return array
     */
    public function login(Request $request, AuthAdminServices $services): array
    {
        $input = $request->post();
        $validation = Validator::make($input, [
            'name' => 'required|string|max:10',
            'password' => 'required|string|max:16'
        ]);
        if ($validation->fails()) {
            return $this->appResponse::error($validation->errors()->first());
        }
        $userBackResult = $services->loginAdminUser($input);
        if (empty($userBackResult)) {
            return $this->appResponse::error('账号不存在或密码不正确');
        }
        return $userBackResult;
    }

    /**
     * 管理员账号登出
     *
     * @return array
     */
    public function out(): array
    {
        $this->tokensUtils::clearAdminUserCache($this->adminUserInfo->id);
        return $this->appResponse::success(null, '账户已退出');
    }
}
