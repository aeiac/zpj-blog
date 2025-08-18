<?php

namespace App\Utils\Response;

use App\Const\Admin\CodeConst;
use Symfony\Component\HttpFoundation\JsonResponse;

class AppResponse
{
    /**
     * 返回成功的 JSON 响应
     *
     * @param mixed  $data 响应数据，支持任意类型，默认 null
     * @param string $msg  响应消息，默认 "success"
     * @param int    $code HTTP 状态码，默认 200
     * @return JsonResponse
     */
    public static function success(mixed $data = null, string $msg = 'success', int $code = 200): JsonResponse
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
     * 返回错误的 JSON 响应
     *
     * @param mixed  $data 响应数据，支持任意类型，默认空数组
     * @param string $msg  响应消息，默认 "error"
     * @param int    $code HTTP 状态码，默认 400
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
     * 返回成功响应的数组格式
     *
     * @param array  $data 响应数据，必须是数组
     * @param string $msg  响应消息，默认 "success"
     * @param int    $code HTTP 状态码，默认 200
     * @return array
     */
    public static function successToArray(array $data=null, string $msg = 'success', int $code = 200): array
    {
        return [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data ?? [],
        ];
    }

    /**
     * 返回错误响应的数组格式
     *
     * @param array|null $data 响应数据，支持 null，默认 null
     * @param string     $msg  响应消息，默认 "error"
     * @param int        $code HTTP 状态码，默认 400
     * @return array
     */
    public static function errorToArray(?array $data = null, string $msg = 'error', int $code = 400): array
    {
        if ($code != 400) {
            $msg = CodeConst::getErrorCodeConstMessages($code);
        }
        return [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data ?? [],
        ];
    }
}
