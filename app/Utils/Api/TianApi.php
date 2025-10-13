<?php

namespace App\Utils\Api;

use App\Utils\Curl\CurlClientUtils;
use App\Utils\Response\AppResponse;

class TianApi
{
    private static string $url = '';
    private static string $key = '';

    private static array $data = [];

    /** @noinspection PhpInconsistentReturnPointsInspection */
    public function __construct()
    {

        self::$url = (string)env('TIAN_API_URL');
        self::$key = (string)env('TIAN_API_KEY');
        if (empty(self::$url) || empty(self::$key)) {
            return AppResponse::errorToArray(msg: '天行数据env配置参数异常');
        }
        self::$data += ['key' => self::$key];
    }

    /**
     * 根据身高、体重、年龄和性别计算体脂率（BFR）。
     *
     * @param int $height 身高（单位：厘米）
     * @param int $weight 体重（单位：千克）
     * @param int $age 年龄（单位：岁）
     * @param int $sex 性别（1 表示男性，0 表示女性）
     *
     * @return array 返回包含体脂率计算结果的数组
     */
    public static function bfrsumBFR(int $height, int $weight, int $age, int $sex): array
    {
        $url = self::$url . '/bfrsum/index';
        self::$data  += [
            'height' => $height,
            'weight' => $weight,
            'age' => $age,
            'sex' => $sex,
        ];
        $result = (new CurlClientUtils())->setMethod(CurlClientUtils::METHOD_GET)->setData(self::$data)->get($url);
        return self::verify($result);
    }

    /**
     * 获取指定城市的天气信息。
     *
     * @param string $city 城市标识，可为以下类型之一：
     *                     - 城市天气ID
     *                     - 行政代码
     *                     - 城市名称
     *                     - IP地址
     *
     * @return array 返回包含天气数据的数组
     */
    public static function weather(string $city): array
    {
        $url = self::$url . '/tianqi/index';
        self::$data += [
            'city' => $city,
            'type' => 1
        ];
        $result = (new CurlClientUtils())->setMethod(CurlClientUtils::METHOD_GET)->setData(self::$data)->get($url);
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
