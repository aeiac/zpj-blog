<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations table admin_user_log.
     */
    public function up(): void
    {
        //php artisan make:migration create_system_blacklist_table
        Schema::create('admin_users_log', function (Blueprint $table) {
            $table->id()->comment('表id');
            $table->integer('admin_users_id')->nullable(false)->comment('管理员id');
            $table->string('path')->nullable(false)->comment('请求来源');
            $table->string('request')->nullable(false)->comment('提交参数');
            $table->string('ip')->nullable(false)->comment('操作ip');
            $table->string('ua')->nullable(false)->comment('操作环境');
            $table->timestamp('created_at')->nullable(false)->comment('创建时间');

            $table->comment('管理员操作日志表');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_user_log');
    }
};
