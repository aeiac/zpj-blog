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
        Schema::create('blog_admin_callback_log', function (Blueprint $table) {
            $table->id();

            // —— 通用实体字段 ——
            $table->string('name')->comment('显示名称或业务核心字段');
            $table->string('code')->unique()->nullable()->comment('唯一编码/编号，用于快速检索和外部对接');
            $table->string('type')->nullable()->comment('类型字段，用于区分不同业务分类');
            $table->tinyInteger('status')->default(1)->comment('状态，1=启用，0=禁用，其他状态可扩展');
            $table->boolean('is_deleted')->default(false)->comment('逻辑删除标志，避免硬删除');

            // —— 回调相关字段 ——
            $table->string('env')->default('test')->comment('请求发生的环境，如 dev/test/prod');
            $table->string('third_party')->comment('第三方平台标识，如 wechat、douyin、alipay');
            $table->string('request_url')->comment('回调的完整 URL');
            $table->string('request_method')->comment('请求方法，如 GET、POST');
            $table->json('request_payload')->nullable()->comment('回调请求内容（JSON 格式，可为空）');
            $table->integer('http_status')->nullable()->comment('本端返回给第三方的 HTTP 状态码');
            $table->integer('res_status')->nullable()->comment('回调请求内容状态码');
            $table->json('res_data')->nullable()->comment('本端返回给第三方的数据（JSON 格式）');
            $table->boolean('is_success')->default(false)->comment('回调处理是否成功');
            $table->string('error_message')->nullable()->comment('回调处理失败时的错误信息');
            $table->integer('retry_count')->default(0)->comment('回调处理重试次数，默认 0');
            $table->string('source_ip')->nullable()->comment('第三方发起回调的来源 IP');
            $table->timestamps();

            $table->comment('系统后台接口回调表');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_admin_allback_log');
    }
};
