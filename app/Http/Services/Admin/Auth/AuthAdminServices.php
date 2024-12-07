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
    public function loginAdminUser(array $input): mixed
    {
        $adminUser = AdminUsers::where('name', $input['name'])
            ->first();
        // TODO 逻辑校验
        if (!$adminUser || !Hash::check($input['password'] . $adminUser->salt, $adminUser->password) || $adminUser->status == AdminUsers::STATUS_INACTIVE) {
            return [];
        }
        $token = TokensUtils::getCache($adminUser->id, 'token');
        if ($token) {
            TokensUtils::clearAdminUserCache($adminUser->id);
        }
        $token = Str::random(60);
        $adminUser->makeHidden(['created_at', 'updated_at']);
        $adminUser->token = $token;
        TokensUtils::setCache($adminUser->id, 'token', $token);
        TokensUtils::setCache($token, 'session', json_encode($adminUser, JSON_UNESCAPED_UNICODE));
        return $this->appResponse::success($adminUser);
    }
}
