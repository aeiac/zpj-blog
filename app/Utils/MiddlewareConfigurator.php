<?php


namespace App\Utils;

use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\StreamInterface;
use App\Models\SystemBlackList;
use App\Const\ErrorCodeConst;

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
        $result=[];
        $ipInfo = SystemBlackList::where('ip_address', $ip)
            ->where('status',1)
            ->first();
        if ($ipInfo) {
            $result['code']=ErrorCodeConst::SYSTEM_BLACKLIST_RESTRICTED_ACCESS;
            $result['message']=ErrorCodeConst::getErrorCodeConstMessages($result['code']);
        }
        return $result;
    }
    // TODO 中间件功能

}
