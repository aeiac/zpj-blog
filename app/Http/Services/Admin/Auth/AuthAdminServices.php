<?php

namespace App\Http\Services\Admin\Auth;

use App\Const\Admin\CodeConst;
use App\Http\Services\Admin\BaseAdminServices;
use App\Models\Permission\AdminRole;
use App\Models\Permission\AdminUsersRole;
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
             $data['msg'] = CodeConst::getCodeMsg(CodeConst::LOGIN_USER_NOT_FOUND_OR_PASSWORD);
             return $data;
        }
        $token = TokensUtils::getCache($adminUser->id, 'token');
        if ($token) {
            TokensUtils::clearAdminUserCache($adminUser->id);
        }
        $token = Str::random(180);
        TokensUtils::setCache($adminUser->id, 'token', $token);
        $roleS = AdminUsersRole::findUserId($adminUser->id);
        $role = AdminRole::whereIn('id', array_column($roleS, 'role_id'))->get(['id', 'role_name'])->toArray();
        $adminInfoAll = [
            'info' => $adminUser,
            'role' => [
                'id' => array_column($role, 'id'),
                'name' => array_column($role, 'role_name')
            ]
        ];
        TokensUtils::setCache($token, 'session', json_encode($adminInfoAll, JSON_UNESCAPED_UNICODE));
        $data += [
            'name'            => $adminUser->name,
            'nickname'        => $adminUser->nickname,
            'last_login_time' => $adminUser->last_login_time,
            'token'           => $token,
            'msg'             => 'OK'
        ];
        $adminUser->last_login_ip = request()->ip();
        if (!$adminUser->save()) {
            return $this->appResponse::errorToArray(code: $this->eMsg::DATA_UPDATE_FAILED);
        }
        return $data;
    }

    public function out(int $uid): string
    {
        $result = '';
        $uObj = AdminUsers::find($uid);
        // 判断用户存不存在
        if (empty($uObj)) {
            return CodeConst::getCodeMsg(CodeConst::LOGIN_USER_NOT_FOUND_OR_PASSWORD);
        }
        $uObj->last_login_time = Carbon::now()->format('Y-m-d H-i-s');
        if (!TokensUtils::clearAdminUserCache($uid) && !$uObj->save()) {
            return CodeConst::getCodeMsg(CodeConst::LOGIN_OUT);
        }
        return $result;
    }
}
