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
        Schema::create('blog_links', function (Blueprint $table) {
            $table->id()->comment('主键 ID');

            // 通用字段
            $table->string('name')->comment('文件名称/显示名称');
            $table->tinyInteger('status')->default(1)->comment('状态：0=默认,1=启用,2=禁用');
            $table->string('type', 50)->nullable()->comment('类型：1=业务链接、2=友链、3=链接');
            $table->string('url',255)->comment('链接');
            $table->tinyInteger('is_deleted')->default(0)->comment('逻辑删除状态 0=未删除,1=已删除');
            $table->timestamp('deleted_at')->nullable()->comment('逻辑删除时间');
            $table->bigInteger('created_by')->nullable()->comment('创建人 ID');
            $table->bigInteger('updated_by')->nullable()->comment('更新人 ID');

            // 时间字段
            $table->timestamps();
            $table->comment('连接表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('blog_links');
    }
};
