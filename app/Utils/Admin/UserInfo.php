<?php

namespace App\Utils\Admin;

class UserInfo
{

    public static  function userInfo()
    {
        $adminUserInfo = TokensUtils::getCache(TokensUtils::getBearerToken(), 'session');
        return json_decode($adminUserInfo);
    }
}
