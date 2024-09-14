<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUsersSeeder extends Seeder
{
    /**
     * This is admin_user_table seeder data
     * @return void
     */
    public function run(): void
    {
        DB::table('admin_users')->insert([
            'name' => env('ADMIN_USER_NAME', 'sa'),
            'password' => Hash::make(env('ADMIN_USER_PASSWORD', '123456')),
            'nickname' => env('ADMIN_USER_NICKNAME', '超级管理员'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
