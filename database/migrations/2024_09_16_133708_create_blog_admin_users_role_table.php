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
        Schema::create('blog_admin_users_role', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->unsignedInteger('users_id')->nullable(false)->comment('用户ID');
            $table->unsignedInteger('role_id')->nullable(false)->comment('角色ID');
            $table->unsignedInteger('created_by')->nullable(false)->comment('创建人');
            $table->unsignedInteger('updated_by')->nullable(false)->comment('更新人');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('分类状态:(使用:active, 禁用:inactive)');

            $table->timestamps();

            $table->comment('系统后台用户关联角色表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_admin_users_role');
    }
};
