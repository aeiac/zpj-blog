<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\SystemServicesController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ArticlesController;
use App\Http\Middleware\Admin\GlobalFunMiddleware as AdminGlobalFunMiddleware;

Route::prefix('admin')->name('admin.')->group(function () {
    // 登录模块
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/out', [AuthController::class, 'out'])->name('out');
    });

    // 系统配置模块
    Route::prefix('system')->name('system.')->group(function () {
        // IP封禁功能
        Route::prefix('blacklist')->name('blacklist.')->group(function () {
            Route::get('/list', [SystemServicesController::class, 'blackList'])->name('list');
            Route::post('/save', [SystemServicesController::class, 'blackListSave'])->name('save');
        });
    });

    // 权限管理模块
    Route::prefix('permission')->name('permission.')->group(function () {
        // 管理员管理
        Route::get('/admin/users/list', [PermissionController::class, 'adminUserList'])->name('admin.users.list');
    });

    // 文章管理模块
    Route::prefix('article')->name('article.')->group(function () {
        Route::get('/list', [ArticlesController::class, 'list'])->name('list');

    });
})->middleware(AdminGlobalFunMiddleware::class);

