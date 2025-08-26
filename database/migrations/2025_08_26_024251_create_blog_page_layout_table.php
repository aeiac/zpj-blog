<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 创建 page_layouts 表，用于存储页面布局的配置信息
     */
    public function up(): void
    {
        Schema::create('blog_page_layouts', function (Blueprint $table) {

            $table->bigIncrements('id')->comment('ID');
            $table->string('name')->comment('业务名称');
            $table->tinyInteger('status')->default(1)->comment('状态：0=默认,1=启用,2=禁用');
            $table->string('icon_url',255)->comment('图标链接');
            $table->tinyInteger('is_deleted')->default(0)->comment('逻辑删除状态 0=未删除,1=已删除');
            $table->string('page_name', 100)->comment('页面名称');
            $table->string('type', 100)->comment('类型');

            $table->string('area', 50)->default('deflate')->comment('区域名，如 top_nav、sidebar、content、footer');
            $table->string('position', 50)->default('deflate')->comment('位置，如 top、left、center、bottom、modal');
            $table->text('function_desc')->nullable()->comment('功能说明，该区域的作用描述');
            $table->string('components', 255)->nullable()->comment('组件/元素，如 table、form、chart、logo、menu');
            $table->string('size', 50)->nullable()->comment('尺寸比例，如 240px、60px、auto');
            $table->text('interaction')->nullable()->comment('交互/行为说明，如 点击跳转、展开收起、滚动加载');
            $table->text('remarks')->nullable()->comment('备注信息');

            $table->timestamp('deleted_at')->nullable()->comment('逻辑删除时间');
            $table->bigInteger('created_by')->nullable()->comment('创建人 ID');
            $table->bigInteger('updated_by')->nullable()->comment('更新人 ID');
            $table->timestamps();

            $table->comment('页面布局配置表');

        });
    }

    /**
     * Reverse the migrations.
     * 删除 page_layouts 表
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_page_layouts');
    }
};
