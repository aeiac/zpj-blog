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
        Schema::create('articles_type', function (Blueprint $table) {
            $table->id();
            $table->integer('f_id')->comment('分类父级ID');
            $table->string('type_name')->nullable()->comment('分类名称');
            $table->text('description')->nullable()->comment('分类描述');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('分类状态:(使用:active, 禁用:inactive)');
            $table->unsignedInteger('sort_order')->default(0)->comment('排序顺序');
            $table->string('image')->nullable()->comment('分类图片');
            $table->unsignedBigInteger('created_by')->nullable()->comment('创建分类的用户ID');
            $table->string('meta_title')->nullable()->comment('分类的SEO标题');
            $table->text('meta_description')->nullable()->comment('分类的SEO描述');

            $table->timestamps();

            $table->comment('文章分类表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles_type');
    }
};
