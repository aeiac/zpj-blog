<?php

namespace App\Const\Admin;

class ErrorCodeConst
{

    // 用户登录错误代码：10000
    public const  ADMIN_LOGIN_REPEAT = 10401;
    public const  ADMIN_LOGIN_USERS_FOUND_OR_INCORRECT_PASSWORD = 10402;

    // 系统错误代码：90000
    public const  SYSTEM_BLACKLIST_RESTRICTED_ACCESS = 90402;

    public static function getErrorCodeConstMessages($code)
    {
        $messages = [
            self::ADMIN_LOGIN_REPEAT => __('请勿重新登录！'),
            self::ADMIN_LOGIN_USERS_FOUND_OR_INCORRECT_PASSWORD => __('找不到用户或密码不正确'),
            self::SYSTEM_BLACKLIST_RESTRICTED_ACCESS => __('已限制访问，稍后在试！')
        ];
        return $messages[$code] ?? __('未知错误');

    }

}
