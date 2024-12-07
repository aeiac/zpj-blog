<?php
namespace App\Utils\Response;

class AppResponse
{
    /**
     * 输出成功结果
     * @param mixed $data       - 响应的数据
     * @param string $message   - 响应消息
     * @param int $code         - 响应状态码
     * @return array
     */
    public static function success(mixed $data = null, string $message = 'success', int $code = 200): array
    {
        return [
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
        ];
    }

    /**
     * 输出错误结果
     * @param string $message   - 响应消息
     * @param int $code         - 响应状态码
     * @return array
     */
    public static function error( string $message = 'error', int $code = 400): array
    {
        return [
            'code'    => $code,
            'message' => $message,
        ];
    }

}
