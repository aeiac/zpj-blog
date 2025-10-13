<?php

namespace App\Utils\Api;

use App\Utils\Curl\CurlClientUtils;
use App\Utils\Response\AppResponse;

class TianApi
{
    private static string $url = '';
    private static string $key = '';

    public function __construct()
    {

        self::$url = (string)env('TIAN_API_URL');
        self::$key = (string)env('TIAN_API_KEY');
        if (empty(self::$url) || empty(self::$key)) {
            return AppResponse::errorToArray(msg: '天行数据env配置参数异常');
        }
    }

    public static function bfrsumBFR(int $height, int $weight, int $age, int $sex): array
    {
        $url = self::$url . '/bfrsum/index';
        $data = [
            'key' => self::$key,
            'height' => $height,
            'weight' => $weight,
            'age' => $age,
            'sex' => $sex,
        ];
        $result = (new CurlClientUtils())->setMethod(CurlClientUtils::METHOD_GET)->setData($data)->get($url);
        return self::verify($result);
    }

    private static function verify(string $result): array
    {
        $result = json_decode($result, true);
        if (empty($result)) {
            return AppResponse::errorToArray(msg: '接口返回参数为空');
        }
        if ($result['code'] !== 200) {
            $msg = $result['msg'] ?? $result['message'] ?? '';
            return AppResponse::errorToArray(msg: $msg);
        }
        return $result['result'];
    }
}
