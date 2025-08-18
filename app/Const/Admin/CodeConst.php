<?php

namespace App\Const\Admin;

class CodeConst
{

    // =======================
    // 用户登录错误代码：10000 系列
    // =======================
    public const LOGIN_REPEAT = 10401;                             // 重复登录
    public const LOGIN_USER_NOT_FOUND_OR_PASSWORD = 10402;         // 用户不存在
    public const LOGIN_ACCOUNT_DISABLED = 10403;                   // 账户被禁用
    public const LOGIN_ACCOUNT_LOCKED = 10404;                     // 账户被锁定
    public const LOGIN_ACCOUNT_EXPIRED = 10405;                    // 账户过期
    public const LOGIN_TOO_MANY_ATTEMPTS = 10406;                  // 登录尝试次数过多

    // =======================
    // 系统错误代码：90000 系列
    // =======================
    public const SYSTEM_BLACKLIST_RESTRICTED_ACCESS = 90402;        // 黑名单限制访问
    public const SYSTEM_MAINTENANCE = 90403;                        // 系统维护
    public const SYSTEM_INTERNAL_ERROR = 90404;                     // 内部错误
    public const SYSTEM_SERVICE_UNAVAILABLE = 90405;                // 服务不可用
    public const SYSTEM_TIMEOUT = 90406;                            // 系统超时

    // =======================
    // 权限/认证错误代码：11000 系列
    // =======================
    public const AUTH_UNAUTHORIZED = 11001;                         // 未授权
    public const AUTH_FORBIDDEN = 11002;                            // 禁止访问
    public const AUTH_TOKEN_EXPIRED = 11003;                        // token 过期
    public const AUTH_INVALID_TOKEN = 11004;                        // token 无效

    // =======================
    // 数据错误代码：12000 系列
    // =======================
    public const DATA_NOT_FOUND = 12001;                             // 数据未找到
    public const DATA_DUPLICATE = 12002;                             // 数据重复
    public const DATA_INVALID = 12003;                               // 数据无效
    public const DATA_SAVE_FAILED = 12004;                           // 数据保存失败
    public const DATA_UPDATE_FAILED = 12005;                         // 数据更新失败
    public const DATA_DELETE_FAILED = 12006;                         // 数据删除失败

    // =======================
    // 方法获取错误提示信息
    // =======================
    public static function getErrorCodeConstMessages($code)
    {
        $messages = [
            // 登录错误
            self::LOGIN_REPEAT => __('请勿重新登录！'),
            self::LOGIN_USER_NOT_FOUND_OR_PASSWORD => __('用户不存在或密码错误！'),
            self::LOGIN_ACCOUNT_DISABLED => __('账户已被禁用！'),
            self::LOGIN_ACCOUNT_LOCKED => __('账户已被锁定！'),
            self::LOGIN_ACCOUNT_EXPIRED => __('账户已过期！'),
            self::LOGIN_TOO_MANY_ATTEMPTS => __('尝试登录次数过多，请稍后再试！'),

            // 系统错误
            self::SYSTEM_BLACKLIST_RESTRICTED_ACCESS => __('已限制访问，稍后再试！'),
            self::SYSTEM_MAINTENANCE => __('系统维护中，请稍后访问！'),
            self::SYSTEM_INTERNAL_ERROR => __('系统内部错误！'),
            self::SYSTEM_SERVICE_UNAVAILABLE => __('服务暂时不可用！'),
            self::SYSTEM_TIMEOUT => __('系统请求超时！'),

            // 权限认证错误
            self::AUTH_UNAUTHORIZED => __('未授权，请登录！'),
            self::AUTH_FORBIDDEN => __('禁止访问！'),
            self::AUTH_TOKEN_EXPIRED => __('登录已过期，请重新登录！'),
            self::AUTH_INVALID_TOKEN => __('无效的登录信息！'),

            // 数据错误
            self::DATA_NOT_FOUND => __('未找到相关数据！'),
            self::DATA_DUPLICATE => __('数据已存在，不能重复！'),
            self::DATA_INVALID => __('数据无效！'),
            self::DATA_SAVE_FAILED => __('保存数据失败！'),
            self::DATA_UPDATE_FAILED => __('更新数据失败！'),
            self::DATA_DELETE_FAILED => __('删除数据失败！'),
        ];

        return $messages[$code] ?? __('未知错误');
    }
}
