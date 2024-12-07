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
        Schema::create('blog_system_blacklist', function (Blueprint $table) {
            $table->id()->comment('表id');
            $table->string('ip_address',255)->unique()->nullable(false)->comment('IP地址');
            $table->string('reason',255)->nullable(false)->comment('封禁IP');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('封禁状态:(1:启用, 0:禁用)');
            $table->timestamps();

            $table->comment('系统封禁黑名单表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_blacklist');
    }
};
