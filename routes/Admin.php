<?php

use Illuminate\Support\Facades\Route;

// 登录模块
Route::prefix('login')->group(function () {
    Route::post('/', 'LoginAdminUsersController@login');
    Route::post('/out', 'LoginAdminUsersController@out');
});

// 系统配置模块
Route::prefix('system')->group(function () {
    // IP封禁功能
    Route::prefix('blacklist')->group(function () {
        Route::get('/list', 'SystemAdminServicesController@blackList');
        Route::post('/save', 'SystemAdminServicesController@blackListSave');
    });
});

// 文章管理模块
Route::prefix('article')->group(function () {
    Route::get('/list', 'ArticlesAdminController@list');
    
});
