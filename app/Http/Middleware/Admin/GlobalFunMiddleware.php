<?php

namespace App\Http\Middleware\Admin;

use App\Utils\MiddlewareConfigurator;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GlobalFunMiddleware
{
    /**
     * 计算接口耗时
     * @var float
     */
    protected float $startTime;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('app.calculate_api_time')) {
            $this->startTime = microtime(true);
        }
        if(config('app.system_ip_ban')){
            $restrictedAccessResult=MiddlewareConfigurator::checkBackList($request->ip());
            if(!empty($restrictedAccessResult)){
               return  response()->json($restrictedAccessResult);
            }
        }
        $response = $next($request);
        if (config('app.calculate_api_time')) {
            $content = json_decode((string)$response->getContent(), true);
            if (json_last_error() == JSON_ERROR_NONE && !empty($content)) {
                $updatedBody = MiddlewareConfigurator::countInterfaceTiming($this->startTime, $content);
                return $response->setContent($updatedBody);
            }
        }
        return $response;
    }
}
