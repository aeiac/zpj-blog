<?php

use App\Http\Controllers\Admin\LinkController;
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

    // 链接管理
    Route::prefix('link')->group(function () {
        Route::post('/add', [LinkController::class, 'add']);
        Route::get('/list', [LinkController::class, 'list']);
    });

    // 功能模块
    Route::prefix('utils')->group(function () {
        // 生成
        Route::prefix('generate')->group(function () {
            Route::get('/permission', [UtilsController::class, 'permission']);
        });

        // 文件上传
        Route::prefix('file')->group(function () {
            Route::post('/operate/{file_id}', [UtilsController::class, 'fileOperate']);
            Route::get('/list', [UtilsController::class, 'fileList']);
            Route::post('/upload', [UtilsController::class, 'fileUpload']);

            // 文件分片
            Route::prefix('chunks')->group(function () {
                Route::get('/start', [UtilsController::class, 'fileChunksStart']);
                Route::post('/upload/{file_code}/{chunk_index}', [UtilsController::class, 'fileChunksUpload']);
                Route::post('/merge/{file_code}/{chunk_count}', [UtilsController::class, 'fileChunksMerge']);
                Route::get('/resume/{file_code}', [UtilsController::class, 'fileChunksResume']);
            });
        });
    });


});
