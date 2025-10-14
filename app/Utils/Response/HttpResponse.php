<?php

namespace App\Utils\Response;

use App\Const\Admin\CodeConst;
use Illuminate\Http\JsonResponse;

class HttpResponse
{
    /**
     * 返回成功响应
     *
     * @param mixed|null $data 响应数据
     *
     * @return array
     */
    public static function success(mixed $data = null): array
    {
        return $data;
    }

    /**
     * 返回失败响应
     *
     * @param string $msg 错误消息
     * @param int $code HTTP 状态码
     *
     * @return array
     */
    public static function error(string $msg= 'error', int $code = 400): array
    {
        if ($code != 400) {
            if ($msg == 'error') {
                $msg = CodeConst::getCodeMsg($code);
            }
        }
        return [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data ?? [],
        ];
    }

    /**
     * 返回未授权响应
     *
     * @param string $message 错误消息
     * @param int $statusCode HTTP 状态码
     *
     * @return array
     */
    public static function unauthorized(string $message = '未登录或未授权', int $statusCode = 401): array
    {
        return [
            'code' => $statusCode,
            'msg' => $message,
        ];
    }

    /**
     * 返回无效请求响应
     *
     * @param string $message 错误消息
     * @param int $statusCode HTTP 状态码
     *
     * @return JsonResponse
     */
    public static function badRequest(string $message = '无效请求', int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'code' => $statusCode,
            'msg' => $message,
        ]);
    }
}
