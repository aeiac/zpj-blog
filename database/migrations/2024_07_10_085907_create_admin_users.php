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
       Schema::create('admin_users',function (Blueprint $table){
           $table->id()->comment('id');
           $table->string('name',48)->unique()->comment('账户');
           $table->string('password',256)->comment('密码');
           $table->string('nickname',48)->comment('昵称');
           $table->tinyInteger('status')->unsigned()->default(1)->comment('管理员状态(1:启用, 0:禁用)');
           $table->timestamps();

           $table->comment('管理员表');
       });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_users');
    }
};
