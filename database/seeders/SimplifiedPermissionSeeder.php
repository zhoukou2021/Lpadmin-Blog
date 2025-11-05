<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\LPadmin\Admin;
use App\Models\LPadmin\Role;
use App\Models\LPadmin\Rule;
use App\Models\LPadmin\Option;
use App\Models\LPadmin\User;

class SimplifiedPermissionSeeder extends Seeder
{
    /**
     * 获取后台路由前缀
     */
    private function getRoutePrefix(): string
    {
        return lpadmin_url_prefix();
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 清理现有数据
        $this->cleanupExistingData();

        // 创建统一的权限/菜单数据
        $this->createUnifiedRules();

        // 创建角色
        $this->createRoles();

        // 创建管理员
        $this->createAdmins();
        // 创建用户
        $this->createUsers();

        // 创建系统配置
        $this->createOptions();
    }

    /**
     * 清理现有数据
     */
    private function cleanupExistingData(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            // 清理关联表
            DB::table('admin_roles')->truncate();
            DB::table('role_rules')->truncate();

            // 清理主表（注意：有外键约束的表需要特殊处理）
            DB::table('admins')->truncate();
            DB::table('roles')->truncate();
            DB::table('rules')->truncate();
            DB::table('options')->truncate();
        } catch (\Exception) {
            // 如果TRUNCATE失败，使用DELETE方式
            $this->cleanupWithDelete();
        } finally {
            // 重新启用外键检查
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    /**
     * 使用DELETE方式清理数据（备选方案）
     */
    private function cleanupWithDelete(): void
    {
        // 清理关联表
        DB::table('admin_roles')->delete();
        DB::table('role_rules')->delete();

        // 清理主表
        DB::table('admins')->delete();
        DB::table('roles')->delete();
        DB::table('rules')->delete();
        DB::table('options')->delete();

        // 重置自增ID（可选）
        DB::statement('ALTER TABLE admin_roles AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE role_rules AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE admin_logs AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE admins AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE roles AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE rules AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE options AUTO_INCREMENT = 1');
    }

    /**
     * 创建统一的权限/菜单规则
     */
    private function createUnifiedRules(): void
    {
        $rules = [
            // ==================== 仪表盘 ====================
            [
                'name' => 'dashboard',
                'title' => '仪表盘',
                'type' => 'menu',
                'icon' => 'layui-icon-home',
                'url' => $this->getRoutePrefix() . '/dashboard',
                'is_show' => 1,
                'sort' => 1000,
                'children' => [
                    ['name' => 'dashboard.statistics', 'title' => '获取统计数据', 'type' => 'api', 'is_show' => 0],
                    ['name' => 'dashboard.system_info', 'title' => '获取系统信息', 'type' => 'api', 'is_show' => 0],
                    ['name' => 'dashboard.recent_logins', 'title' => '获取最近登录', 'type' => 'api', 'is_show' => 0],
                ]
            ],

            // ==================== 系统管理 ====================
            [
                'name' => 'system',
                'title' => '系统管理',
                'type' => 'menu',
                'icon' => 'layui-icon-set',
                'url' => '#', // 目录类型菜单
                'is_show' => 1,
                'sort' => 900,
                'children' => [
                    [
                        'name' => 'admin',
                        'title' => '管理员管理',
                        'type' => 'menu',
                        'icon' => 'layui-icon-username',
                        'url' => $this->getRoutePrefix() . '/admin',
                        'is_show' => 1,
                        'sort' => 900,
                        'children' => [
                            ['name' => 'admin.index', 'title' => '管理员列表', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'admin.create', 'title' => '创建管理员', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'admin.store', 'title' => '保存管理员', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'admin.edit', 'title' => '编辑管理员', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'admin.update', 'title' => '更新管理员', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'admin.destroy', 'title' => '删除管理员', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'admin.toggle_status', 'title' => '切换状态', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'admin.reset_password', 'title' => '重置密码', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'admin.batch_delete', 'title' => '批量删除', 'type' => 'button', 'is_show' => 0],
                        ]
                    ],
                    [
                        'name' => 'role',
                        'title' => '角色管理',
                        'type' => 'menu',
                        'icon' => 'layui-icon-group',
                        'url' => $this->getRoutePrefix() . '/role',
                        'is_show' => 1,
                        'sort' => 800,
                        'children' => [
                            ['name' => 'role.index', 'title' => '角色列表', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'role.create', 'title' => '创建角色', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'role.store', 'title' => '保存角色', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'role.edit', 'title' => '编辑角色', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'role.update', 'title' => '更新角色', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'role.destroy', 'title' => '删除角色', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'role.select', 'title' => '角色选择', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'role.toggle_status', 'title' => '切换状态', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'role.permissions', 'title' => '权限管理', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'role.update_permissions', 'title' => '更新权限', 'type' => 'api', 'is_show' => 0],
                        ]
                    ],
                    [
                        'name' => 'rule',
                        'title' => '权限规则',
                        'type' => 'menu',
                        'icon' => 'layui-icon-vercode',
                        'url' => $this->getRoutePrefix() . '/rule',
                        'is_show' => 1,
                        'sort' => 700,
                        'children' => [
                            ['name' => 'rule.index', 'title' => '权限列表', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'rule.create', 'title' => '创建权限', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'rule.store', 'title' => '保存权限', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'rule.edit', 'title' => '编辑权限', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'rule.update', 'title' => '更新权限', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'rule.destroy', 'title' => '删除权限', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'rule.select', 'title' => '权限选择', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'rule.toggle_status', 'title' => '切换状态', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'rule.tree', 'title' => '权限树', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'rule.permission_tree', 'title' => '权限树结构', 'type' => 'api', 'is_show' => 0],
                        ]
                    ],
                    [
                        'name' => 'menu',
                        'title' => '菜单管理',
                        'type' => 'menu',
                        'icon' => 'layui-icon-menu-fill',
                        'url' => $this->getRoutePrefix() . '/menu',
                        'is_show' => 1,
                        'sort' => 600,
                        'children' => [
                            ['name' => 'menu.index', 'title' => '菜单列表', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'menu.create', 'title' => '创建菜单', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'menu.store', 'title' => '保存菜单', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'menu.edit', 'title' => '编辑菜单', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'menu.update', 'title' => '更新菜单', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'menu.destroy', 'title' => '删除菜单', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'menu.select', 'title' => '菜单选择', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'menu.updateSort', 'title' => '更新排序', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'menu.batchDestroy', 'title' => '批量删除', 'type' => 'button', 'is_show' => 0],
                        ]
                    ],
                ]
            ],

            // ==================== 用户管理 ====================
            [
                'name' => 'user',
                'title' => '用户管理',
                'type' => 'menu',
                'icon' => 'layui-icon-user',
                'url' => $this->getRoutePrefix() . '/user',
                'is_show' => 1,
                'sort' => 850,
                'children' => [
                    ['name' => 'user.index', 'title' => '用户列表', 'type' => 'api', 'is_show' => 0],
                    ['name' => 'user.create', 'title' => '创建用户', 'type' => 'button', 'is_show' => 0],
                    ['name' => 'user.store', 'title' => '保存用户', 'type' => 'api', 'is_show' => 0],
                    ['name' => 'user.show', 'title' => '查看用户', 'type' => 'button', 'is_show' => 0],
                    ['name' => 'user.edit', 'title' => '编辑用户', 'type' => 'button', 'is_show' => 0],
                    ['name' => 'user.update', 'title' => '更新用户', 'type' => 'api', 'is_show' => 0],
                    ['name' => 'user.destroy', 'title' => '删除用户', 'type' => 'button', 'is_show' => 0],
                    ['name' => 'user.select', 'title' => '用户选择', 'type' => 'api', 'is_show' => 0],
                    ['name' => 'user.toggle_status', 'title' => '切换状态', 'type' => 'button', 'is_show' => 0],
                    ['name' => 'user.batch_delete', 'title' => '批量删除', 'type' => 'button', 'is_show' => 0],
                    ['name' => 'user.statistics', 'title' => '用户统计', 'type' => 'api', 'is_show' => 0],
                ]
            ],

            // ==================== 博客管理 ====================
            [
                'name' => 'blog',
                'title' => '博客管理',
                'type' => 'menu',
                'icon' => 'layui-icon-template-1',
                'url' => '#',
                'is_show' => 1,
                'sort' => 800,
                'children' => [
                    [
                        'name' => 'blog.post',
                        'title' => '博客列表',
                        'type' => 'menu',
                        'icon' => null,
                        'url' => $this->getRoutePrefix() . '/blog/post',
                        'is_show' => 1,
                        'sort' => 800,
                        'children' => [
                            ['name' => 'blog.post.index', 'title' => '文章列表', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'blog.post.create', 'title' => '创建文章', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'blog.post.store', 'title' => '保存文章', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'blog.post.edit', 'title' => '编辑文章', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'blog.post.update', 'title' => '更新文章', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'blog.post.destroy', 'title' => '删除文章', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'blog.post.select', 'title' => '文章选择', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'blog.post.batch_delete', 'title' => '批量删除', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'blog.post.batch_status', 'title' => '批量更新状态', 'type' => 'button', 'is_show' => 0],
                        ]
                    ],
                    [
                        'name' => 'blog.category',
                        'title' => '博客分类',
                        'type' => 'menu',
                        'icon' => null,
                        'url' => $this->getRoutePrefix() . '/blog/category',
                        'is_show' => 1,
                        'sort' => 700,
                        'children' => [
                            ['name' => 'blog.category.index', 'title' => '分类列表', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'blog.category.create', 'title' => '创建分类', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'blog.category.store', 'title' => '保存分类', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'blog.category.edit', 'title' => '编辑分类', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'blog.category.update', 'title' => '更新分类', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'blog.category.destroy', 'title' => '删除分类', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'blog.category.select', 'title' => '分类选择', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'blog.category.batch_delete', 'title' => '批量删除', 'type' => 'button', 'is_show' => 0],
                        ]
                    ],
                    [
                        'name' => 'blog.tag',
                        'title' => '博客标签',
                        'type' => 'menu',
                        'icon' => null,
                        'url' => $this->getRoutePrefix() . '/blog/tag',
                        'is_show' => 1,
                        'sort' => 600,
                        'children' => [
                            ['name' => 'blog.tag.index', 'title' => '标签列表', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'blog.tag.create', 'title' => '创建标签', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'blog.tag.store', 'title' => '保存标签', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'blog.tag.edit', 'title' => '编辑标签', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'blog.tag.update', 'title' => '更新标签', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'blog.tag.destroy', 'title' => '删除标签', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'blog.tag.select', 'title' => '标签选择', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'blog.tag.batch_delete', 'title' => '批量删除', 'type' => 'button', 'is_show' => 0],
                        ]
                    ],
                    [
                        'name' => 'blog.comment',
                        'title' => '博客评论',
                        'type' => 'menu',
                        'icon' => null,
                        'url' => $this->getRoutePrefix() . '/blog/comment',
                        'is_show' => 1,
                        'sort' => 500,
                        'children' => [
                            ['name' => 'blog.comment.index', 'title' => '评论列表', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'blog.comment.approve', 'title' => '审核评论', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'blog.comment.block', 'title' => '屏蔽评论', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'blog.comment.destroy', 'title' => '删除评论', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'blog.comment.batch_delete', 'title' => '批量删除', 'type' => 'button', 'is_show' => 0],
                        ]
                    ],
                    [
                        'name' => 'blog.navs',
                        'title' => '顶部菜单',
                        'type' => 'menu',
                        'icon' => null,
                        'url' => $this->getRoutePrefix() . '/blog/nav',
                        'is_show' => 1,
                        'sort' => 400,
                        'children' => [
                            ['name' => 'blog.nav.index', 'title' => '菜单列表', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'blog.nav.create', 'title' => '创建菜单', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'blog.nav.store', 'title' => '保存菜单', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'blog.nav.edit', 'title' => '编辑菜单', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'blog.nav.update', 'title' => '更新菜单', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'blog.nav.destroy', 'title' => '删除菜单', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'blog.nav.batch_delete', 'title' => '批量删除', 'type' => 'button', 'is_show' => 0],
                        ]
                    ],
                    [
                        'name' => 'blog.ad',
                        'title' => '广告管理',
                        'type' => 'menu',
                        'icon' => 'layui-icon-senior',
                        'url' => $this->getRoutePrefix() . '/blog/ad',
                        'is_show' => 1,
                        'sort' => 300,
                        'children' => [
                            ['name' => 'blog.ad.index', 'title' => '广告列表', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'blog.ad.create', 'title' => '创建广告', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'blog.ad.store', 'title' => '保存广告', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'blog.ad.edit', 'title' => '编辑广告', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'blog.ad.update', 'title' => '更新广告', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'blog.ad.destroy', 'title' => '删除广告', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'blog.ad.batch_delete', 'title' => '批量删除', 'type' => 'button', 'is_show' => 0],
                        ]
                    ],
                    [
                        'name' => 'deepseek',
                        'title' => '生成文章',
                        'type' => 'menu',
                        'icon' => null,
                        'url' => $this->getRoutePrefix() . '/blog/deepseek/config',
                        'is_show' => 1,
                        'sort' => 200,
                        'children' => [
                            ['name' => 'deepseek.config', 'title' => '配置页面', 'type' => 'button', 'is_show' => 0],
                            ['name' => 'deepseek.config.store', 'title' => '保存配置', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'deepseek.config.data', 'title' => '获取配置', 'type' => 'api', 'is_show' => 0],
                            ['name' => 'deepseek.generate', 'title' => '生成文章', 'type' => 'button', 'is_show' => 0],
                        ]
                    ],
                ]
            ],

            // ==================== 系统配置 ====================
            [
                'name' => 'config',
                'title' => '系统配置',
                'type' => 'menu',
                'icon' => 'layui-icon-set-sm',
                'url' => $this->getRoutePrefix() . '/config',
                'is_show' => 1,
                'sort' => 750,
                'children' => [
                    ['name' => 'config.system', 'title' => '系统设置', 'type' => 'menu', 'is_show' => 1,'url' => $this->getRoutePrefix() . '/config/system/settings',
                    'children' => [
                        ['name' => 'config.saveSystem', 'title' => '保存系统设置', 'type' => 'api', 'is_show' => 0],
                    ]],             
                    ['name' => 'config.index', 'title' => '配置列表', 'type' => 'menu', 'is_show' => 1,'url' => $this->getRoutePrefix() . '/config',
                    'children' => [
                        ['name' => 'config.create', 'title' => '创建配置', 'type' => 'button', 'is_show' => 0],
                        ['name' => 'config.store', 'title' => '保存配置', 'type' => 'api', 'is_show' => 0],
                        ['name' => 'config.show', 'title' => '查看配置', 'type' => 'button', 'is_show' => 0],
                        ['name' => 'config.edit', 'title' => '编辑配置', 'type' => 'button', 'is_show' => 0],
                        ['name' => 'config.update', 'title' => '更新配置', 'type' => 'api', 'is_show' => 0],
                        ['name' => 'config.destroy', 'title' => '删除配置', 'type' => 'button', 'is_show' => 0],
                        ['name' => 'config.select', 'title' => '配置选择', 'type' => 'api', 'is_show' => 0],
                        ['name' => 'config.batchDestroy', 'title' => '批量删除', 'type' => 'button', 'is_show' => 0],
                        ['name' => 'config.batchUpdate', 'title' => '批量更新', 'type' => 'api', 'is_show' => 0],
                        ['name' => 'config.export', 'title' => '配置导出', 'type' => 'button', 'is_show' => 0],
                        ['name' => 'config.import', 'title' => '配置导入', 'type' => 'api', 'is_show' => 0],
                        ['name' => 'config.importPage', 'title' => '导入页面', 'type' => 'button', 'is_show' => 0],
                    ]],
                    
                    ['name' => 'config.groups.page', 'title' => '分组管理', 'type' => 'menu', 'is_show' => 1,'url' => $this->getRoutePrefix() . '/config/groups/page',
                    'children' => [
                        ['name' => 'config.groups.index', 'title' => '配置分组', 'type' => 'api', 'is_show' => 0],
                        ['name' => 'config.groups.create', 'title' => '创建分组', 'type' => 'api', 'is_show' => 0],
                        ['name' => 'config.groups.update', 'title' => '更新分组', 'type' => 'api', 'is_show' => 0],
                        ['name' => 'config.groups.delete', 'title' => '删除分组', 'type' => 'api', 'is_show' => 0],
                        ['name' => 'config.groups.batch_delete', 'title' => '批量删除分组', 'type' => 'api', 'is_show' => 0],
                        ['name' => 'config.groups.show', 'title' => '查看分组', 'type' => 'api', 'is_show' => 0],
                    ]],
                ]
            ],

            

            // ==================== 缓存管理 ====================
            [
                'name' => 'cache',
                'title' => '缓存管理',
                'type' => 'menu',
                'icon' => 'layui-icon-engine',
                'url' => $this->getRoutePrefix() . '/cache',
                'is_show' => 1,
                'sort' => 650,
                'children' => [
                    ['name' => 'cache.index', 'title' => '缓存首页', 'type' => 'menu', 'is_show' => 1,'url' => $this->getRoutePrefix() . '/cache',
                    'children' => [
                        ['name' => 'cache.stats', 'title' => '缓存统计', 'type' => 'api', 'is_show' => 0],
                        ['name' => 'cache.clearByType', 'title' => '按类型清理', 'type' => 'button', 'is_show' => 0],
                        ['name' => 'cache.clearAll', 'title' => '清理所有', 'type' => 'button', 'is_show' => 0],
                        ['name' => 'cache.clearConfig', 'title' => '清理配置缓存', 'type' => 'button', 'is_show' => 0],
                        ['name' => 'cache.warmupConfig', 'title' => '预热配置缓存', 'type' => 'button', 'is_show' => 0],
                    ]],
                    
                    ['name' => 'cache.monitor', 'title' => '缓存监控', 'type' => 'menu', 'is_show' => 1,'url' => $this->getRoutePrefix() . '/cache/monitor',
                    'children' => [
                         ['name' => 'cache.monitorData', 'title' => '监控数据', 'type' => 'api', 'is_show' => 0],
                        ['name' => 'cache.keys', 'title' => '缓存键列表', 'type' => 'api', 'is_show' => 0],
                        ['name' => 'cache.deleteKey', 'title' => '删除缓存键', 'type' => 'button', 'is_show' => 0],
                        ['name' => 'cache.getValue', 'title' => '获取缓存值', 'type' => 'api', 'is_show' => 0],
                        ['name' => 'cache.setValue', 'title' => '设置缓存值', 'type' => 'api', 'is_show' => 0],
                    ]],
                   
                    ['name' => 'cache.settings', 'title' => '缓存设置', 'type' => 'menu', 'is_show' => 1,'url' => $this->getRoutePrefix() . '/cache/settings',
                    'children' => [
                        ['name' => 'cache.updateSettings', 'title' => '更新设置', 'type' => 'api', 'is_show' => 0],
                        ['name' => 'cache.getSettings', 'title' => '获取设置', 'type' => 'api', 'is_show' => 0],
                        ['name' => 'cache.testConnection', 'title' => '测试连接', 'type' => 'api', 'is_show' => 0],
                    ]],
                    
                ]
            ],


            
        ];

        $this->insertRulesRecursively($rules);
    }

    /**
     * 递归插入权限规则
     */
    private function insertRulesRecursively($rules, $parentId = 0): void
    {
        foreach ($rules as $rule) {
            $children = $rule['children'] ?? [];
            unset($rule['children']);

            $rule['parent_id'] = $parentId;
            $rule['status'] = 1;
            $rule['target'] = $rule['target'] ?? '_self';
            $rule['is_show'] = $rule['is_show'] ?? 1;
            $rule['sort'] = $rule['sort'] ?? 0;
            $rule['created_at'] = now();
            $rule['updated_at'] = now();

            $ruleId = DB::table('rules')->insertGetId($rule);

            if (!empty($children)) {
                $this->insertRulesRecursively($children, $ruleId);
            }
        }
    }

    /**
     * 创建角色
     */
    private function createRoles(): void
    {
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => '超级管理员',
                'description' => '拥有系统所有权限',
                'status' => 1,
                'sort' => 1000,
            ],
            [
                'name' => 'admin',
                'display_name' => '系统管理员',
                'description' => '拥有系统管理权限',
                'status' => 1,
                'sort' => 900,
            ],
            [
                'name' => 'operator',
                'display_name' => '操作员',
                'description' => '拥有基础操作权限',
                'status' => 1,
                'sort' => 800,
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::create($roleData);

            // 根据角色分配权限
            if ($role->name === 'super_admin') {
                // 超级管理员分配所有权限
                $allRuleIds = Rule::pluck('id')->toArray();
                $role->rules()->sync($allRuleIds);
            } elseif ($role->name === 'admin') {
                // 系统管理员分配除了敏感操作外的所有权限
                $adminRuleIds = Rule::whereNotIn('name', [
                    'admin.destroy', 'admin.reset_password', 'role.destroy'
                ])->pluck('id')->toArray();
                $role->rules()->sync($adminRuleIds);
            } elseif ($role->name === 'operator') {
                // 操作员分配基础权限
                $operatorRuleIds = Rule::whereIn('name', [
                    'dashboard',
                    'user', 'user.create', 'user.update', 'user.toggle_status', 'user.statistics',
                    'config', 'config.select',
                ])->pluck('id')->toArray();
                $role->rules()->sync($operatorRuleIds);
            }
        }
    }

    /**
     * 创建管理员
     */
    private function createAdmins(): void
    {
        $admins = [
            [
                'username' => 'admin',
                'nickname' => '超级管理员',
                'email' => 'admin@lpadmin.com',
                'password' => Hash::make('123456'),
                'avatar' => '/static/admin/images/avatar.jpg',
                'status' => 1,
                'role_name' => 'super_admin',
            ],
            [
                'username' => 'operator',
                'nickname' => '操作员',
                'email' => 'operator@lpadmin.com',
                'password' => Hash::make('123456'),
                'avatar' => '/static/admin/images/avatar.jpg',
                'status' => 1,
                'role_name' => 'operator',
            ],
        ];

        foreach ($admins as $adminData) {
            $roleName = $adminData['role_name'];
            unset($adminData['role_name']);

            $admin = Admin::create($adminData);

            // 分配角色
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $admin->roles()->attach($role->id);
            }
        }
    }
    /**
     * 创建用户
     */
    private function createUsers(): void
    {
        $users = [
            [
                'username' => 'test1',
                'nickname' => '测试1',
                'email' => 'test@lpadmin.com',
                'password' => Hash::make('123456'),
                'avatar' => '/static/admin/images/avatar.jpg',
                'status' => 1,
                'gender' => 0,
                'phone' => '15737185100',
                'remark' => '测试测试',
            ],
            [
                'username' => 'test2',
                'nickname' => '测试2',
                'email' => 'test2@lpadmin.com',
                'password' => Hash::make('123456'),
                'avatar' => '/static/admin/images/avatar.jpg',
                'status' => 1,
                'gender' => 1,
                'phone' => '15737185101',
                'remark' => '测试测试2',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }

    /**
     * 创建系统配置
     */
    private function createOptions(): void
    {
        $options = [
            // ==================== 系统配置 ====================
            [
                'group' => 'system',
                'name' => 'system_author',
                'title' => '系统作者',
                'value' => 'LPadmin Team',
                'type' => 'text',
                'description' => '系统作者',
                'sort' => 6
            ],
            [
                'group' => 'system',
                'name' => 'system_copyright',
                'title' => '版权信息',
                'value' => '© 2024 LPadmin. All rights reserved.',
                'type' => 'text',
                'description' => '版权信息',
                'sort' => 7
            ],
            [
                'group' => 'system',
                'name' => 'lang',
                'title' => '多语言',
                'value' => 'cn,en,tw',
                'type' => 'checkbox',
                'options' => json_encode([
                    'cn' => '简体',
                    'en' => '英文',
                    'tw' => '繁体',
                ]),
                'description' => '多语言',
                'sort' => 8
            ],
            [
                'group' => 'system',
                'name' => 'site_name',
                'title' => '网站名称',
                'value' => json_encode(['cn' => 'LPadmin博客', 'en' => 'LPadmin Blog', 'tw' => 'LPadmin博客']),
                'type' => Option::TYPE_TEXT,
                'options' => null,
                'description' => '网站名称',
                'sort' => 1,
                'is_i18n' => true
            ],
            [
                'group' => 'system',
                'name' => 'site_title',
                'title' => 'SEO标题',
                'value' => json_encode(['cn' => 'PHP Laravel ThinkPHP Bootstrap jQuery HTML5 CSS3 开发经验与源码分享', 'en' => 'PHP, Laravel, ThinkPHP, Bootstrap, jQuery, HTML5, CSS3 Development Experience & Source Code Sharing', 'tw' => 'PHP Laravel ThinkPHP Bootstrap jQuery HTML5 CSS3 開發經驗與源碼分享']),
                'type' => Option::TYPE_TEXT,
                'options' => null,
                'description' => '网站SEO标题',
                'sort' => 2,
                'is_i18n' => true
            ],
            [
                'group' => 'system',
                'name' => 'site_keywords',
                'title' => 'SEO关键词',
                'value' => json_encode(['cn' => 'PHP,Laravel,ThinkPHP,Bootstrap,jQuery,HTML5,CSS3,开发经验,源码分享', 'en' => 'PHP,Laravel,ThinkPHP,Bootstrap,jQuery,HTML5,CSS3,Development Experience,Source Code Sharing', 'tw' => 'PHP,Laravel,ThinkPHP,Bootstrap,jQuery,HTML5,CSS3,開發經驗,源碼分享']),
                'type' => Option::TYPE_TEXTAREA,
                'options' => null,
                'description' => '网站SEO关键词',
                'sort' => 3,
                'is_i18n' => true
            ],
            [
                'group' => 'system',
                'name' => 'site_description',
                'title' => 'SEO描述',
                'value' => json_encode(['cn' => 'LPadmin博客是一个专注于PHP、Laravel、ThinkPHP等技术栈的开发经验分享与源码下载平台', 'en' => 'LPadmin Blog is a platform for sharing PHP, Laravel, ThinkPHP development experience and source code downloads', 'tw' => 'LPadmin博客是一個專注於PHP、Laravel、ThinkPHP等技術棧的開發經驗分享與源碼下載平台']),
                'type' => Option::TYPE_TEXTAREA,
                'options' => null,
                'description' => '网站SEO描述',
                'sort' => 4,
                'is_i18n' => true
            ],
            [
                'group' => 'system',
                'name' => 'site_logo',
                'title' => '网站logo',
                'value' => '',
                'type' => Option::TYPE_IMAGE,
                'options' => null,
                'description' => null,
                'sort' => 5,
                'is_i18n' => false
            ],
            [
                'group' => 'contact',
                'name' => 'contact_info',
                'title' => '联系信息',
                'value' => json_encode(['cn' => '<p>欢迎联系我们！</p><p>邮箱：admin@example.com</p><p>电话：400-000-0000</p>', 'en' => '<p>Welcome to contact us!</p><p>Email: admin@example.com</p><p>Phone: 400-000-0000</p>', 'tw' => '<p>歡迎聯繫我們！</p><p>郵箱：admin@example.com</p><p>電話：400-000-0000</p>']),
                'type' => Option::TYPE_RICHTEXT,
                'options' => null,
                'description' => '配置联系信息',
                'sort' => 1,
                'is_i18n' => true
            ],
            [
                'group' => 'system',
                'name' => 'sensitive_word',
                'title' => '敏感词',
                'value' => '共产党|法轮功|习近平',
                'type' => Option::TYPE_TEXTAREA,
                'options' => null,
                'description' => '评论内容敏感词过滤，多个敏感词使用 | 隔开',
                'sort' => 9,
                'is_i18n' => false
            ],
            [
                'group' => 'system',
                'name' => 'comment_check',
                'title' => '评论审核',
                'value' => '0',
                'type' => Option::TYPE_SWITCH,
                'options' => null,
                'description' => '是否开启评论审核后才显示',
                'sort' => 10,
                'is_i18n' => false
            ],

            // ==================== 缓存配置 ====================
            [
                'group' => 'cache',
                'name' => 'cache_driver',
                'title' => '缓存驱动',
                'value' => 'file',
                'type' => 'select',
                'options' => json_encode([
                    'file' => '文件缓存',
                    'redis' => 'Redis缓存',
                    'memcached' => 'Memcached缓存',
                    'database' => '数据库缓存',
                    'array' => '数组缓存（仅测试）'
                ]),
                'description' => '选择缓存存储驱动',
                'sort' => 100
            ],
            [
                'group' => 'cache',
                'name' => 'cache_ttl',
                'title' => '默认TTL',
                'value' => '3600',
                'type' => 'number',
                'description' => '缓存默认过期时间，单位：秒（60-86400秒）',
                'sort' => 90
            ],
            [
                'group' => 'cache',
                'name' => 'cache_prefix',
                'title' => '缓存前缀',
                'value' => 'lpadmin_',
                'type' => 'text',
                'description' => '用于区分不同应用的缓存，避免键名冲突',
                'sort' => 80
            ],
            [
                'group' => 'cache',
                'name' => 'cache_enable_compression',
                'title' => '启用压缩',
                'value' => '0',
                'type' => 'switch',
                'description' => '对缓存数据进行压缩存储',
                'sort' => 70
            ],
            [
                'group' => 'cache',
                'name' => 'cache_auto_clear',
                'title' => '自动清理',
                'value' => '1',
                'type' => 'switch',
                'description' => '自动清理过期缓存',
                'sort' => 60
            ],
            [
                'group' => 'cache',
                'name' => 'cache_clear_interval',
                'title' => '清理间隔',
                'value' => '3600',
                'type' => 'number',
                'description' => '自动清理缓存的间隔时间，单位：秒',
                'sort' => 50
            ],

            // ==================== DeepSeek AI 配置 ====================
            [
                'group' => 'deepseek',
                'name' => 'deepseek_api_key',
                'title' => 'DeepSeek API Key',
                'value' => '',
                'type' => Option::TYPE_TEXT,
                'options' => null,
                'description' => 'DeepSeek API 密钥，用于调用 AI 生成接口',
                'sort' => 100
            ],
            [
                'group' => 'deepseek',
                'name' => 'deepseek_auto_enabled',
                'title' => '开启自动生成',
                'value' => '1',
                'type' => Option::TYPE_SWITCH,
                'options' => null,
                'description' => '是否开启每天自动生成文章',
                'sort' => 99
            ],
            [
                'group' => 'deepseek',
                'name' => 'deepseek_auto_publish',
                'title' => '自动发布',
                'value' => '0',
                'type' => Option::TYPE_SWITCH,
                'options' => null,
                'description' => '生成的文章是否自动发布（否则保存为草稿）',
                'sort' => 98
            ],
            [
                'group' => 'deepseek',
                'name' => 'deepseek_daily_count_min',
                'title' => '每天最少生成条数',
                'value' => '1',
                'type' => Option::TYPE_NUMBER,
                'options' => null,
                'description' => '每天自动生成文章的最少数量',
                'sort' => 97
            ],
            [
                'group' => 'deepseek',
                'name' => 'deepseek_daily_count_max',
                'title' => '每天最多生成条数',
                'value' => '3',
                'type' => Option::TYPE_NUMBER,
                'options' => null,
                'description' => '每天自动生成文章的最多数量',
                'sort' => 96
            ],
            [
                'group' => 'deepseek',
                'name' => 'deepseek_keywords',
                'title' => '关键词列表',
                'value' => "php\r\nlaravel\r\nthinkphp\r\nredis\r\nmysql\r\ndcat\r\nfastadmin\r\ncrmeb\r\nhtml5\r\ncss3\r\njquery\r\njs\r\nuniapp\r\n小程序\r\n代码\r\n源码\r\n模板\r\nblade\r\nworkerman\r\nwebman\r\nlayui\r\nlaravel-admin\r\ngo",
                'type' => Option::TYPE_TEXTAREA,
                'options' => null,
                'description' => '用于生成文章的关键词，每行一个',
                'sort' => 95
            ],
            [
                'group' => 'deepseek',
                'name' => 'deepseek_prompt_rules',
                'title' => '生成规则/提示词',
                'value' => $this->getDefaultPromptRulesForSeeder(),
                'type' => Option::TYPE_TEXTAREA,
                'options' => null,
                'description' => '文章生成的提示词模板，用于指导 AI 生成内容',
                'sort' => 94
            ],
            [
                'group' => 'deepseek',
                'name' => 'deepseek_model',
                'title' => 'AI 模型',
                'value' => 'deepseek-chat',
                'type' => Option::TYPE_TEXT,
                'options' => null,
                'description' => '使用的 DeepSeek 模型名称',
                'sort' => 93
            ],
        ];

        foreach ($options as $option) {
            // 检查配置是否已存在，避免重复插入
            $exists = Option::where('group', $option['group'])
                ->where('name', $option['name'])
                ->exists();
            
            if (!$exists) {
                Option::create($option);
            }
        }
    }

    /**
     * 获取默认提示词规则（用于Seeder，匹配数据库中的实际格式）
     */
    private function getDefaultPromptRulesForSeeder(): string
    {
        return "你是一位专业的博客文章写作专家。请根据以下信息生成一篇高质量的博客文章：\r\n\r\n分类：{category_name}\r\n关键词：{keyword}\r\n标签：{tags}\r\n\r\n要求：\r\n1. 文章标题要有吸引力，符合SEO优化，标题中要包含具体的问题\r\n2. 摘要控制在150-200字，概括文章核心内容\r\n3. 正文内容不少于800字，结构清晰，段落分明，内容要围绕标题中的具体问题回答\r\n4. SEO标题要与文章标题相关但可以略有不同\r\n5. SEO关键词包含：{keyword} 及相关标签\r\n6. SEO描述要在150字以内，突出文章价值\r\n7. 内容要原创、专业、有实用价值\r\n\r\n生成格式要求（非常重要）：\r\n- 必须使用严格符合JSON规范的格式返回，不要包含任何其他文字说明\r\n- JSON包含字段：title, summary, content, meta_title, meta_keywords, meta_description\r\n- 所有字段都是字符串类型\r\n- 字符串中的所有换行符、制表符等特殊字符必须使用转义序列（\\\\n, \\\\t, \\\\r），不能使用实际的换行符\r\n- 确保返回的是完整且有效的JSON，可以直接被json_decode解析\r\n\r\n请开始生成文章，只返回JSON格式数据，不要有任何其他文字：";
    }
}
