<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminInitialSeeder extends Seeder
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

        // 页面管理
        DB::table('blog_page_layouts')->insert([
            [
                'name' => '文章管理',
                'status' => 1,
                'icon_url' => '/icons/article.svg',
                'path_url' => '../views/Article/Index.vue', // 有页面路径
                'page_name' => 'ArticlePage',
                'type' => '业务模块',
                'area' => 'sidebar',
                'position' => 'left',
                'function_desc' => '用于管理文章的增删改查、发布与分类',
                'components' => 'table,form',
                'size' => 'auto',
                'interaction' => '点击操作、分页加载',
                'remarks' => '核心内容管理模块',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '权限管理',
                'status' => 1,
                'icon_url' => '/icons/permission.svg',
                // path_url 选填，不生成
                'page_name' => 'PermissionPage',
                'type' => '系统模块',
                'area' => 'sidebar',
                'position' => 'left',
                'function_desc' => '用于分配用户角色和权限',
                'components' => 'table,dialog',
                'size' => 'auto',
                'interaction' => '点击弹窗、编辑保存',
                'remarks' => '系统安全核心模块',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '系统管理',
                'status' => 1,
                'icon_url' => '/icons/system.svg',
                'path_url' => '../views/System/Index.vue', // 有页面路径
                'page_name' => 'SystemPage',
                'type' => '系统模块',
                'area' => 'sidebar',
                'position' => 'left',
                'function_desc' => '系统基础配置，如日志、参数、备份等',
                'components' => 'table,form',
                'size' => 'auto',
                'interaction' => '分页加载、保存配置',
                'remarks' => '系统维护管理',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '界面管理',
                'status' => 1,
                'icon_url' => '/icons/ui.svg',
                // path_url 选填，不生成
                'page_name' => 'InterfacePage',
                'type' => '界面模块',
                'area' => 'content',
                'position' => 'center',
                'function_desc' => '用于配置系统界面布局、主题和组件',
                'components' => 'form,card',
                'size' => 'auto',
                'interaction' => '点击切换主题',
                'remarks' => '前端展示配置模块',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '用户管理',
                'status' => 1,
                'icon_url' => '/icons/user.svg',
                // path_url 选填，不生成
                'is_deleted' => 0,
                'page_name' => 'UserPage',
                'type' => '业务模块',
                'area' => 'sidebar',
                'position' => 'left',
                'function_desc' => '用于管理系统用户信息与状态',
                'components' => 'table,form',
                'size' => 'auto',
                'interaction' => '分页加载、编辑保存',
                'remarks' => '基础用户管理模块',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

    }
}
