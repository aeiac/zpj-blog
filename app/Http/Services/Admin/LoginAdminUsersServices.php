<?php

namespace App\Http\Services\Admin;

use App\Const\CacheConst;
use App\Models\AdminUsers;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginAdminUsersServices
{
    /**
     * Token时效
     */
    protected const TIMEOUT = 84600; // 秒

    /**
     * 登录逻辑
     * @param array $input
     * @return mixed
     */
    public function loginAdminUser(array $input): mixed
    {
        $adminUser = AdminUsers::where('name', $input['name'])
            ->first();
        if (!$adminUser || !Hash::check($input['password'], $adminUser->password)) {
            return [];
        }
        $adminUser->makeHidden(['password']);
        $token = Str::random(60);
        self::setTokenKey($adminUser->id, $token);
        $adminUser->tokne = $token;
        return $adminUser;
    }

    /**
     * 设置Token缓存健值
     * @param string $adminUserId
     * @param string $token
     * @return void
     */
    public static function setTokenKey(string $adminUserId, string $token): void
    {
        Cache::set(self::getTokenKey($token), $adminUserId, self::TIMEOUT);
    }

    /**
     * 获取管理员Token缓存键名
     * @param string $token
     * @return string
     */
    public static function getTokenKey(string $token): string
    {
        return sprintf(CacheConst::ADMIN_ACCESS_TOKEN, $token);
    }

    /**
     * 删除管理员Token
     * @param string $token
     * @return bool
     */
    public static function delToken(string $token): bool
    {
        if (Cache::has(self::getTokenKey($token))) {
            Cache::forget(self::getTokenKey($token));
            return true;
        }
        return false;
    }

    /**
     * 验证Token的有效性
     * @param string $token
     * @return bool
     */
    public static function validateToken(string $token): bool
    {
        return Cache::has(self::getTokenKey($token));
    }

    /**
     * 获取当前Token
     * @param string $token
     * @return string
     */
    public static function getToken(string $token): string
    {
        return Cache::get(self::getTokenKey($token));

    }

}
