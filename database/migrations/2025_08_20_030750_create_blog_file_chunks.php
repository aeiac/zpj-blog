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
        Schema::create('blog_files_chunks', function (Blueprint $table) {
            $table->id()->comment('主键 ID');

            // 通用字段
            $table->string('name')->comment('文件名称/显示名称');
            $table->tinyInteger('status')->default(1)->comment('状态：0=草稿,1=启用,2=禁用');
            $table->string('type', 50)->nullable()->comment('类型/业务分类');
            $table->string('code', 50)->unique()->comment('唯一编码/编号');
            $table->tinyInteger('is_deleted')->default(0)->comment('逻辑删除状态 0=未删除,1=已删除');
            $table->timestamp('deleted_at')->nullable()->comment('逻辑删除时间');
            $table->bigInteger('created_by')->nullable()->comment('创建人 ID');
            $table->bigInteger('updated_by')->nullable()->comment('更新人 ID');

            // 分片相关字段
            $table->bigInteger('file_id')->nullable()->comment('原始文件 ID');
            $table->integer('chunk_index')->nullable()->comment('分片序号');
            $table->bigInteger('chunk_size')->nullable()->comment('分片大小(字节)');
            $table->string('path')->comment('分片存储路径或 URL');
            $table->string('chunk_hash', 64)->nullable()->comment('分片校验码');
            $table->tinyInteger('upload_status')->default(0)->comment('上传状态：0=未上传,1=已上传,2=上传失败');
            $table->timestamp('uploaded_at')->nullable()->comment('上传完成时间');

            // 时间字段
            $table->timestamps();
            $table->comment('文件信息表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_files_chunks');
    }
};
