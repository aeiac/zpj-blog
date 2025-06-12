<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\SystemServicesController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ArticlesController;
use App\Http\Controllers\Admin\UtilsController;
use App\Http\Middleware\Admin\GlobalFunMiddleware as AdminGlobalFunMiddleware;

Route::prefix('admin')->middleware([AdminGlobalFunMiddleware::class])->group(function () {
    // 登录模块
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/out', [AuthController::class, 'out']);
    });

    // 系统配置模块
    Route::prefix('system')->group(function () {
        // IP封禁功能
        Route::prefix('blacklist')->group(function () {
            Route::get('/list', [SystemServicesController::class, 'blackList']);
            Route::post('/save', [SystemServicesController::class, 'blackListSave']);
        });
    });

    // 权限管理模块
    Route::prefix('permission')->group(function () {
        Route::get('/admins', [PermissionController::class, 'admins']);
    });

    // 文章管理模块
    Route::prefix('article')->group(function () {
        Route::get('/list', [ArticlesController::class, 'list']);
    });

    // 功能模块
    Route::prefix('utils')->group(function () {
        Route::prefix('generate')->group(function () {
            Route::get('/permission', [UtilsController::class, 'permission']);
        });
    });
});
