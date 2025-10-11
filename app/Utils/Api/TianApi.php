<?php

namespace App\Utils\Api;

use App\Utils\Curl\CurlClientUtils;
use App\Utils\Response\AppResponse;
use Illuminate\Support\Facades\App;

class TianApi
{
    private static string $url;
    private static string $key;

    protected function __construct()
    {
        self::$url = env('TIAN_API_URL');
        self::$key = env('TIAN_API_EKY');
        if(empty(self::$url) || empty(self::$key)){
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

    }



}
