<?php

namespace App\Utils\Admin;

class UserInfo
{

    public static  function userInfo()
    {
        $adminUserInfo = TokensUtils::getCache(TokensUtils::getBearerToken(), 'session');
        return json_decode($adminUserInfo);
    }

    /**
     * 获取完整请求信息
     *
     * @return array
     */
    public static function getRequestData(): array
    {
        $localIp = self::getClientIp();
        $publicIp = self::getPublicIp();

        return [
            'local_ip'  => $localIp,                                                               // 本地 IPv4
            'public_ip' => $publicIp,                                                              // 公网 IPv4
            'method'    => $_SERVER['REQUEST_METHOD'] ?? 'CLI',                                    // 请求方式
            'url'       => $_SERVER['REQUEST_URI'] ?? '',                                          // 请求 URL
            'ua'        => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',                               // User-Agent
            'referer'   => $_SERVER['HTTP_REFERER'] ?? '',                                         // 来源页
            'host'      => $_SERVER['HTTP_HOST'] ?? '',                                            // 主机名
            'scheme'    => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http', // 协议
            'language'  => $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',                                  // 浏览器语言
            'is_ajax'   => isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest', // 是否 Ajax 请求
            'time'      => date('Y-m-d H:i:s'),                                              // 请求时间
        ];
    }

    /**
     * 获取客户端局域网 IPv4
     *
     * @return string
     */
    protected static function getClientIp(): string
    {
        $keys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'REMOTE_ADDR',
        ];

        foreach ($keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                foreach ($ips as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                        return $ip;
                    }
                }
            }
        }

        return '0.0.0.0';
    }

    /**
     * 获取公网 IP
     *
     * @return string
     */
    protected static function getPublicIp(): string
    {
        try {
            // 使用一个简单的公共接口获取公网 IP
            $ip = @file_get_contents('https://api.ipify.org');
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                return $ip;
            }
        } catch (\Throwable $e) {
            // 捕获异常，返回空或默认值
        }
        return '0.0.0.0';
    }


}
