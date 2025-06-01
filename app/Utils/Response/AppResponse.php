<?php

namespace App\Utils\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class AppResponse
{
    /**
     * 输出成功结果（返回 JSON）
     *
     * @param mixed  $data 响应数据
     * @param string $msg  响应消息（默认：success）
     * @param int    $code 响应状态码（默认：200）
     * @return JsonResponse
     */
    public static function success(mixed $data): JsonResponse
    {
        $response = [
            'code' => 200,
            'msg'  => 'success',
            'data' => $data ?? null,
        ];
        return response()->json($response);
    }

    /**
     * 输出错误结果（返回 JSON）
     *
     * @param mixed  $data 响应数据
     * @param string $msg  响应消息（默认：error）
     * @param int    $code 响应状态码（默认：400）
     * @return JsonResponse
     */
    public static function error(mixed $data): JsonResponse
    {
        $response = [
            'code' => 400,
            'msg'  => 'error',
            'data' => $data ?? null,
        ];
        return response()->json($response);
    }

    /**
     * 输出成功结果（返回数组）
     *
     * @param mixed  $data 响应数据
     * @param int    $code 响应状态码（默认：200）
     * @param string $msg  响应消息（默认：success）
     * @return array
     */
    public static function successToArray(mixed $data, string $msg = 'success', int $code = 200): array
    {
        return [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data ?? null,
        ];
    }

    /**
     * 输出错误结果（返回数组）
     *
     * @param int    $code 响应状态码（默认：400）
     * @param string $msg  响应消息（默认：error）
     * @param mixed  $data 响应数据
     * @return array
     */
    public static function errorToArray(int $code = 400, string $msg = 'error', mixed $data): array
    {
        return [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data ?? null,
        ];
    }
}
