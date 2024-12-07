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
        Schema::create('blog_admin_permission', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->unsignedInteger('f_id')->nullable(true)->comment('父级权限');
            $table->unsignedInteger('code')->nullable(false)->comment('权限码');
            $table->string('content')->nullable(false)->comment('权限内容');
            $table->string('name')->nullable(false)->comment('权限名称');
            $table->string('remark')->nullable(true)->comment('备注');
            $table->unsignedInteger('created_by')->nullable(false)->comment('创建人');
            $table->unsignedInteger('updated_by')->nullable(false)->comment('更新人');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('分类状态:(使用:active, 禁用:inactive)');

            $table->timestamps();

            $table->comment('系统后台权限表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_admin_permission');
    }
};
