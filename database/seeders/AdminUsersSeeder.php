<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUsersSeeder extends Seeder
{
    /**
     * This is admin_user_table seeder data
     * @return void
     */
    public function run(): void
    {
        $salt = Str::random(6); // 随机盐

        // 超级管理员用户
        if (!DB::table('blog_admin_users')->where('id', 1)->exists()) {
            DB::table('blog_admin_users')->insert([
                'id'         => 1,
                'name'       => env('ADMIN_USER_NAME', 'admin'),
                'password'   => Hash::make(env('ADMIN_USER_PASSWORD', '123456') . $salt),
                'nickname'   => env('ADMIN_USER_NICKNAME', '超级管理员'),
                'salt'       => $salt,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 超级管理员角色
        if (!DB::table('blog_admin_role')->where('id', 1)->exists()) {
            DB::table('blog_admin_role')->insert([
                'id'         => 1,
                'code'       => 0,
                'role_name'  => 'Administrator',
                'remark'     => '超级管理员无视所有权限',
                'created_by' => 0,
                'updated_by' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 超级管理员用户角色关联
        if (!DB::table('blog_admin_users_role')
            ->where('users_id', 1)
            ->where('role_id', 1)
            ->exists())
        {
            DB::table('blog_admin_users_role')->insert([
                'users_id'   => 1,
                'role_id'    => 1,
                'created_by' => 0,
                'updated_by' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
