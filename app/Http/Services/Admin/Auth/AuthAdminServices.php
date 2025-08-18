<?php

namespace App\Http\Services\Admin\Auth;

use App\Http\Services\Admin\BaseAdminServices;
use App\Utils\Admin\TokensUtils;
use App\Models\AdminUsers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthAdminServices extends BaseAdminServices
{
    /**
     * 管理员用户登录逻辑
     *
     * 根据用户名和密码验证管理员用户身份，
     * 验证通过后生成新的登录令牌（token）并缓存，
     * 返回登录用户信息和token。
     *
     * @param array $input 登录请求参数，必须包含 'name' 和 'password' 字段
     *
     * @return array 返回响应数组，包含登录状态和数据或错误消息
     */
    public function login(array $input): array
    {
        $data = [];
        $adminUser = AdminUsers::where('name', $input['name'])->first();
        // 校验密码
        if (!$adminUser || !Hash::check($input['password'] . $adminUser->salt, $adminUser->password) || $adminUser->status == AdminUsers::STATUS_INACTIVE) {
            // 账号或密码错误
            return $this->appResponse::errorToArray(code: $this->eMsg::LOGIN_USER_NOT_FOUND_OR_PASSWORD);
        }
        $token = TokensUtils::getCache($adminUser->id, 'token');
        if ($token) {
            TokensUtils::clearAdminUserCache($adminUser->id);
        }
        $token = Str::random(60);
        TokensUtils::setCache($adminUser->id, 'token', $token);
        TokensUtils::setCache($token, 'session', json_encode($adminUser, JSON_UNESCAPED_UNICODE));
        $data += [
            'name'            => $adminUser->name,
            'nickname'        => $adminUser->nickname,
            'last_login_time' => $adminUser->last_login_time,
            'token'           => $token
        ];
        $adminUser->last_login_ip = request()->ip();
        if (!$adminUser->save()) {
            return $this->appResponse::errorToArray(code: $this->eMsg::DATA_UPDATE_FAILED);
        }
        return $this->appResponse::successToArray($data);
    }

    public function out(int $uid): array
    {
        $uObj = AdminUsers::find($uid);
        if (empty($uObj)) {
            return $this->appResponse::errorToArray(msg: '无此用户');
        }
        $uObj->last_login_time = Carbon::now()->format('Y-m-d H-i-s');;
        if (!TokensUtils::clearAdminUserCache($uid) && !$uObj->save()) {
            return $this->appResponse::errorToArray(msg: '退出失败');
        }
        return $this->appResponse::successToArray(msg: '退出成功');
    }
}
