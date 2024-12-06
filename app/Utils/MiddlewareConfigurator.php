<?php


namespace App\Utils;

use App\Const\Admin\ErrorCodeConst;
use App\Const\Admin\RedisKeyConst;
use App\Models\Systems\SystemBlackList;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Support\Facades\Redis;
use Psr\Http\Message\StreamInterface;

class MiddlewareConfigurator
{


    /**
     * 统计接口耗时
     * @param float $startTime
     * @param array $content
     * @return StreamInterface
     */
    public static function countInterfaceTiming(float $startTime, array $content): StreamInterface
    {
        $endTime = microtime(true);
        $executionTime = ((float)$endTime - $startTime) * 1000;
        if ($executionTime < 1000) {
            $content['api_time'] = round($executionTime, 2) . ' ms';
        } elseif ($executionTime < 60000) {
            $content['api_time'] = round($executionTime / 1000, 2) . ' s';
        } else {
            $content['api_time'] = round($executionTime / 60000, 2) . ' min';
        }
        return Utils::streamFor(json_encode($content));
    }

    /**
     * 限制封禁IP访问
     * @param string $ip
     * @return array
     */
    public static function checkBackList(string $ip): array
    {
        $result = array();
        $cacheKey = sprintf(RedisKeyConst::ACCESS_BLACK_LIST_KEY, $ip);
        $ipCache = Redis::get($cacheKey);
        if (!$ipCache) {
            $ipInfo = SystemBlackList::where([
                'ip_address'=>$ip,
                'status'=>SystemBlackList::STATUS_ACTIVE
            ])->first();
            if ($ipInfo) {
                Redis::set($cacheKey, $ipInfo->ip_address, 'EX', RedisKeyConst::ACCESS_BLACK_LIST_KEY_EXPIRATION_TIME);
            }
        }
        if ($ipCache || $ipInfo) {
            $result['code'] = ErrorCodeConst::SYSTEM_BLACKLIST_RESTRICTED_ACCESS;
            $result['message'] = ErrorCodeConst::getErrorCodeConstMessages($result['code']);
        }
        return $result;
    }
}
