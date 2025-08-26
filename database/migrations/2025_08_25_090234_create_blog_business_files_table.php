<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::create('blog_files_business', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->string('name')->comment('文件名称/显示名称');
            $table->string('code', 50)->unique()->comment('唯一编码/编号');
            $table->tinyInteger('is_deleted')->default(0)->comment('逻辑删除状态 0=未删除,1=已删除');
            $table->timestamp('deleted_at')->nullable()->comment('逻辑删除时间');
            $table->string('business_type', 50)->comment('业务类型（如: article, user, order 等）');
            $table->unsignedBigInteger('business_id')->comment('业务主键ID');
            $table->unsignedBigInteger('file_id')->comment('文件ID（关联文件表）');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->unsignedBigInteger('created_by')->default(0)->comment('创建人');
            $table->unsignedBigInteger('updated_by')->default(0)->comment('更新人');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('状态: active=正常, inactive=禁用');
            $table->timestamps();
            $table->comment('业务关联文件表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('blog_files_business');

    }
};
