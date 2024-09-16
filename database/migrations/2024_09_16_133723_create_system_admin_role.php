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
        Schema::create('system_admin_role', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->unsignedInteger('code')->nullable(false)->comment('角色码');
            $table->string('role_name')->unique()->nullable(false)->comment('角色名称');
            $table->string('remark')->nullable(true)->comment('角色备注');
            $table->tinyInteger('fixed')->default(0)->comment('是否为固定角色');
            $table->unsignedInteger('created_by')->nullable(false)->comment('创建人');
            $table->unsignedInteger('updated_by')->nullable(false)->comment('更新人');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('分类状态:(使用:active, 禁用:inactive)');
            $table->timestamps();

            $table->comment('系统后台用户角色表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_admin_role');
    }
};
