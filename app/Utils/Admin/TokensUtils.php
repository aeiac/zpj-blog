<?php
/**
 * @file   - TokensUtils
 * @refer  - 用于配置管理员Tokens缓存
 * -
 * @author - Mr.raycake
 * @date   - 2024-12-07 22:26:32
 */

namespace App\Utils\Admin;

use App\Const\Admin\CacheConst;
use Illuminate\Support\Facades\Cache;

class TokensUtils
{
    // 获取缓存键名
    public static function getCacheKey(string $uniV, string $type): string
    {
        $validTypes = [
            'token' => CacheConst::ADMIN_ACCESS_TOKENS,
            'session' => CacheConst::ADMIN_ACCESS_SESSION,
        ];
        return sprintf($validTypes[$type], $uniV);
    }

    // 设置缓存
    public static function setCache(string $uniV, string $type, mixed $value = null): void
    {
        $key = self::getCacheKey($uniV, $type);
        Cache::set($key, $value, CacheConst::ADMIN_ACCESS_TIMEOUT);
    }

    // 删除缓存
    public static function delCache(string $uniV, string $type): bool
    {
        $key = self::getCacheKey($uniV, $type);
        return Cache::forget($key);
    }

    // 验证缓存是否存在
    public static function validateCache(string $uniV, string $type): bool
    {
        $key = self::getCacheKey($uniV, $type);
        return Cache::has($key);
    }

    // 获取缓存
    public static function getCache(string $uniV, string $type)
    {
        $key = self::getCacheKey($uniV, $type);
        return Cache::get($key);
    }

    // 额外功能: 清理管理员用户所有缓存
    public static function clearAdminUserCache(string $id)
    {
        self::delCache(self::getCache($id, 'token'), 'session');
        self::delCache($id, 'token');
    }
}
