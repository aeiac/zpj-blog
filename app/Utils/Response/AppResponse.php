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
    public static function success(mixed $data = null, string $msg = 'success', int $code = 200): JsonResponse
    {
        $response = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data ?? null,
        ];

        if (is_array($data) && isset($data['code'])) {
            $response['code'] = $data['code'] ?? $code;
            $response['msg']  = $data['msg'] ?? $msg;
            $response['data'] = $data['data'] ?? null;
        }

        return response()->json($response);
    }


    /**
     * 输出错误结果（返回 JSON）
     *
     * @param mixed  $data 响应数据
     * @param string $msg  响应消息（默认：error）
     * @param int    $code 响应状态码（默认：200）
     * @return JsonResponse
     */
    public static function error(mixed $data = [], string $msg = 'error', int $code = 400): JsonResponse
    {
        $response = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data ?? [],
        ];
        if (is_array($data) && isset($data['code'])) {
            $response['code'] = $data['code'] ?? $code;
            $response['msg']  = $data['msg'] ?? $msg;
            $response['data'] = $data['data'] ?? [];
        }
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
    public static function errorToArray(mixed $data = null, string $msg = 'error', int $code = 400): array
    {
        return [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data ?? null,
        ];
    }
}
