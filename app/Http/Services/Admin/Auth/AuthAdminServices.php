<?php

namespace App\Http\Services\Admin\Auth;

use App\Http\Services\Admin\BaseAdminServices;
use App\Utils\Admin\TokensUtils;
use App\Models\AdminUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthAdminServices extends BaseAdminServices
{
    /**
     * 登录逻辑
     *
     * @param array $input
     *
     * @return mixed
     */
    public function loginAdminUser(array $input): array
    {
        $data = [];
        $adminUser = AdminUsers::where('name', $input['name'])->first();
        if (!$adminUser || !Hash::check($input['password'] . $adminUser->salt, $adminUser->password) || $adminUser->status == AdminUsers::STATUS_INACTIVE) {
            return $this->appResponse::errorToArray(msg: '登录失败，密码错误！');
        }
        $token = TokensUtils::getCache($adminUser->id, 'token');
        if ($token) {
            TokensUtils::clearAdminUserCache($adminUser->id);
        }
        $token = Str::random(60);
        $adminUser->token = $token;
        TokensUtils::setCache($adminUser->id, 'token', $token);
        TokensUtils::setCache($token, 'session', json_encode($adminUser, JSON_UNESCAPED_UNICODE));
        $data += [
            'name'            => $adminUser->name,
            'nickname'        => $adminUser->nickname,
            'last_login_time' => $adminUser->last_login_time,
            'token'           => $token
        ];
        return $this->appResponse::successToArray($data, '登录成功');
    }
}
