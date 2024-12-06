<?php

namespace App\Const\Admin;

class RedisKeyConst
{
    // 黑名单列表
    const ACCESS_BLACK_LIST_KEY = "access_black_list:%s";
    const ACCESS_BLACK_LIST_KEY_EXPIRATION_TIME = 600;

}
