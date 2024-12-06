<?php

namespace App\Utils\Redis;
use Illuminate\Support\Facades\Redis;

class RedisCache
{
    /**
     * 设置 Redis 值
     */
    public static function set(string $key, $value, int $expiration = 3600)
    {
        Redis::set($key, $value, 'EX', $expiration);
    }

    /**
     * 获取 Redis 值
     */
    public static function get(string $key)
    {
        return Redis::get($key);
    }

    /**
     * 删除 Redis 值
     */
    public static function del(string $key): void
    {
        Redis::del($key);
    }
}
