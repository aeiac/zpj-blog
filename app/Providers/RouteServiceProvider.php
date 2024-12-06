<?php
/**
 * This file is Router part of.
 * Mainly used for router Registration Services.
 *
 * @author  Mr.ZhanPinjie
 * @created 2024-07-09
 */

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use App\Http\Middleware\Admin\GlobalFunMiddleware as AdminGlobalFunMiddleware ;

class RouteServiceProvider extends ServiceProvider
{

    /**
     * 配置默认主页
     * @var string
     */
    public const HOME = '/home';

    /**
     * 根据命名空间加载路由
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * 初始化应用程序路由
     *
     * @return void
     */
    public function boot(): void
    {
        $this->routes(function () {
            Route::namespace($this->namespace . '\Admin')
                ->group(base_path('routes/Admin.php'));
            Route::namespace($this->namespace . '\V1')
                ->group(base_path('routes/V1.php'));
        });

    }
}
