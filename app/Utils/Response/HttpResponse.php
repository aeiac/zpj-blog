<?php

namespace App\Utils\Response;

use Illuminate\Http\JsonResponse;

class HttpResponse
{
    /**
     * 返回成功响应
     *
     * @param mixed|null $data 响应数据
     *
     * @return JsonResponse
     */
    public static function success(mixed $data = null): JsonResponse
    {
        return response()->json($data);
    }

    /**
     * 返回失败响应
     *
     * @param string $message 错误消息
     * @param int $statusCode HTTP 状态码
     *
     * @return JsonResponse
     */
    public static function error(string $message = '请求失败', int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'code' => $statusCode,
            'message' => $message,
        ]);
    }

    /**
     * 返回未授权响应
     *
     * @param string $message 错误消息
     * @param int $statusCode HTTP 状态码
     *
     * @return JsonResponse
     */
    public static function unauthorized(string $message = '未授权', int $statusCode = 401): JsonResponse
    {
        return response()->json([
            'code' => $statusCode,
            'message' => $message,
        ]);
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
            'message' => $message,
        ]);
    }
}
