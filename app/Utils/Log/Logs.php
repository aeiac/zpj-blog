<?php

namespace App\Utils\Log;

use Illuminate\Support\Facades\Log;

class Logs
{
    protected static string $channel = 'daily_callback'; // 使用自定义通道

    /**
     * 写 info 日志（按天生成文件）
     */
    public static function info(string $module, string $message, array $data = [], array $context = [])
    {
        $log = sprintf(
            "[%s] | 模块: %s | 消息: %s | 请求数据: %s | 请求上下文: %s",
            strtoupper('INFO'),
            $module,
            $message,
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
        Log::channel(self::$channel)->info($log);
    }

    /**
     * 写 warning 日志
     */
    public static function warning(string $message, array $context = []): void
    {
        Log::channel(self::$channel)->warning($message . ' :', $context);
    }

    /**
     * 写 error 日志
     */
    public static function error(string $message, array $context = []): void
    {
        Log::channel(self::$channel)->error($message . ' :', $context);
    }

    /**
     * 写 debug 日志
     */
    public static function debug(string $message, array $context = []): void
    {
        Log::channel(self::$channel)->debug($message . ' :', $context);
    }

    /**
     * 写自定义级别日志
     */
    public static function log(string $level, string $message, array $context = []): void
    {
        Log::channel(self::$channel)->log($level, $message . ' :', $context);
    }
}
