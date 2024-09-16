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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('article_id')->comment('文章ID');
            $table->string('title', 255)->comment('文章标题');
            $table->string('slug', 255)->unique()->comment('文章slug');
            $table->text('content')->comment('内容');
            $table->unsignedInteger('author_id')->comment('作者ID');
            $table->unsignedInteger('type_id')->comment('分类ID');
            $table->enum('status', ['draft', 'published', 'archived', 'del'])->nullable()->default('draft')->comment('发布状态:(草稿、已发布、已归档、软删除)');
            $table->timestamp('published_at')->nullable()->comment('发布时间');
            $table->timestamps();

            $table->comment('文章表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
