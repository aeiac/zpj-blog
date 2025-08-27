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
        Schema::create('blog_articles', function (Blueprint $table) {
            $table->id();

            $table->string('code', 64)->nullable()->unique()->comment('文章编码');
            $table->string('title', 255)->comment('文章标题');
            $table->string('slug', 255)->unique()->comment('文章slug');
            $table->string('secret', 100)->nullable()->comment('密码');
            $table->boolean('is_secret')->default(false)->comment('是否加密');
            $table->mediumtext('content')->comment('内容');
            $table->unsignedBigInteger('author_id')->comment('作者ID');
            $table->unsignedInteger('type_id')->comment('分类ID');
            $table->string('type', 50)->nullable()->comment('文章类型');
            $table->tinyInteger('status')->default(0)->comment('文章状态：0=回收站、1=草稿、2=默认、4=待发布、5=已发布、6=已下线、7=禁用、8=定时发布、11=精选、12=私密、13=推流文章');
            $table->unsignedInteger('sort')->default(0)->comment('排序值');
            $table->timestamp('published_at')->nullable()->comment('发布时间');
            $table->boolean('is_deleted')->default(false)->comment('是否软删除');
            $table->unsignedBigInteger('created_by')->nullable()->comment('创建人ID');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('更新人ID');
            $table->timestamps();

            $table->comment('文章表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_articles');
    }
};
