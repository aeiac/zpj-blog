<?php

namespace App\Const\Admin;

class CacheConst
{
    // 管理员Tokens
    const ADMIN_ACCESS_TOKENS = "admin_access_tokens:%s";
    // 管理员用户信息
    const ADMIN_ACCESS_SESSION = "admin_access_session:%s";
    // 管理员过期时间
    const ADMIN_ACCESS_TIMEOUT = 84600; // 单位: (秒)


}
