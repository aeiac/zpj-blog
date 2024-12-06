<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\LoginBaseAdminUsersController;
use App\Http\Controllers\Admin\SystemBaseAdminServicesController;
use App\Http\Controllers\Admin\SystemBaseAdminPermissionController;
use App\Http\Controllers\Admin\ArticlesBaseAdminController;
use App\Http\Middleware\Admin\GlobalFunMiddleware as AdminGlobalFunMiddleware;

Route::prefix('admin')->group(function () {
    // 登录模块
    Route::prefix('login')->group(function () {
        Route::post('/', [LoginBaseAdminUsersController::class, 'login'])->name('admin.login');
        Route::post('/out', [LoginBaseAdminUsersController::class, 'out'])->name('admin.logout');
    });

    // 系统配置模块
    Route::prefix('system')->group(function () {
        // IP封禁功能
        Route::prefix('blacklist')->group(function () {
            Route::get('/list', [SystemBaseAdminServicesController::class, 'blackList'])->name('admin.system.black.list');
            Route::post('/save', [SystemBaseAdminServicesController::class, 'blackListSave'])->name('admin.system.black.save');
        })->name('admin.system.blacklist');
    })->name('admin.system');

    // 权限管理模块
    Route::prefix('permission')->group(function () {
        // 管理员管理
        Route::get('/admin/users/list', [SystemBaseAdminPermissionController::class, 'adminUserList'])->name('admin.permission.admin.users.list');
    });

    // 文章管理模块
    Route::prefix('article')->group(function () {
        Route::get('/list', [ArticlesBaseAdminController::class, 'list'])->name('admin.article.list');

    });
})->middleware(AdminGlobalFunMiddleware::class);

