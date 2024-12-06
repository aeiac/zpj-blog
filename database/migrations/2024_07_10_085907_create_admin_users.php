<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blog_admin_users', function (Blueprint $table) {
            $table->id()->comment('id');
            $table->string('name', 48)->unique()->comment('账户');
            $table->string('password', 255)->comment('密码');
            $table->string('nickname', 48)->nullable()->comment('昵称');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('管理员状态(1:启用, 0:禁用)');
            $table->string('salt', 30)->comment('密码盐');
            $table->string('avatar', 512)->nullable()->comment('头像');
            $table->string('phone', 15)->nullable()->unique()->comment('电话');
            $table->string('email', 120)->nullable()->unique()->comment('邮箱');
            $table->string('last_login_ip', 45)->nullable()->comment('最近登录 IP');
            $table->timestamp('last_login_time')->nullable()->useCurrent()->comment('最后登录时间');

            $table->timestamps();
            $table->comment('管理员表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_users');
    }
};
