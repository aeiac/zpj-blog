<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_files', function (Blueprint $table) {
            $table->id()->comment('主键 ID');
            $table->string('code')->unique()->comment('文件唯一编码');
            $table->string('name')->comment('通用名称，例如业务名');
            $table->string('file_name')->comment('文件名称');
            $table->string('md5_hash')->nullable()->comment('文件 MD5 值');
            $table->text('file_path')->comment('文件存储路径');
            $table->string('file_type')->comment('文件类型');
            $table->string('file_extension', 10)->comment('文件后缀');
            $table->unsignedBigInteger('file_size')->comment('文件大小（字节）');
            $table->dateTime('upload_time')->comment('上传时间');
            $table->boolean('is_deleted')->default(false)->comment('是否删除：0=否，1=是');
            $table->unsignedBigInteger('uploader_id')->comment('上传人 ID');
            $table->string('storage_type')->comment('存储方式，例如 local=本地、oss=文件服务器、');
            $table->string('business_tag')->comment('业务标签标识');
            $table->text('remark')->nullable()->comment('备注信息');
            $table->dateTime('expire_at')->nullable()->comment('文件过期时间');
            $table->unsignedInteger('download_count')->default(0)->comment('下载次数');
            $table->tinyInteger('status')->default(1)->comment('状态（1=启用，0=禁用）');
            $table->unsignedBigInteger('created_by')->comment('创建人 ID');
            $table->unsignedBigInteger('updated_by')->comment('更新人 ID');
            $table->softDeletes()->comment('软删除时间');
            $table->timestamps();

            $table->comment('文件信息表');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_files');
    }
};
