<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Services\Admin\LoginAdminUsersServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class LoginAdminUsersController extends AdminController
{
    protected static array $excludedAuth = ['login'];

    /**
     * 后台管理员登录
     * @param Request $request
     * @param LoginAdminUsersServices $services
     * @return array
     */
    public function login(Request $request, LoginAdminUsersServices $services): array
    {
        $input = $request->post();
        $validation = Validator::make($input, [
            'name' => 'required|string|max:10',
            'password' => 'required|string|max:16'
        ]);
        if ($validation->fails()) {
            return $this->error(400, $validation->errors()->first());
        }
        if ($services->validateToken($this->accessToken)) {
            return $this->error(400, '请勿重新登录！');
        }
        $userBackResult = $services->loginAdminUser($input);
        if (empty($userBackResult)) {
            return $this->error(400, '账号不存在或密码不正确！');
        }
        return $this->success($userBackResult);
    }

    /**
     * 管理员账号登出
     * @param LoginAdminUsersServices $services
     * @return array
     */
    public function out(LoginAdminUsersServices $services): array
    {
        if ($this->accessToken && $services->delToken($this->accessToken)) {
            return $this->success();
        }
        return $this->error(400, '登出异常');
    }
}
