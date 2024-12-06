<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\LoginAdminUsersController;
use App\Http\Controllers\Admin\SystemAdminServicesController;
use App\Http\Controllers\Admin\SystemAdminPermissionController;
use App\Http\Controllers\Admin\ArticlesAdminController;

// 登录模块
Route::prefix('login')->group(function () {
    Route::post('/', [LoginAdminUsersController::class, 'login'])->name('admin.login');
    Route::post('/out', [LoginAdminUsersController::class, 'out'])->name('admin.logout');
});

// 系统配置模块
Route::prefix('system')->group(function () {
    // IP封禁功能
    Route::prefix('blacklist')->group(function () {
        Route::get('/list', [SystemAdminServicesController::class, 'blackList'])->name('admin.system.black.list');
        Route::post('/save', [SystemAdminServicesController::class, 'blackListSave'])->name('admin.system.black.save');
    });
});

// 权限管理模块
Route::prefix('permission')->group(function () {
    // 管理员管理
    Route::get('/admin/users/list', [SystemAdminPermissionController::class, 'adminUserList'])->name('admin.permission.admin.users.list');
});

// 文章管理模块
Route::prefix('article')->group(function () {
    Route::get('/list', [ArticlesAdminController::class, 'list'])->name('admin.article.list');

});
