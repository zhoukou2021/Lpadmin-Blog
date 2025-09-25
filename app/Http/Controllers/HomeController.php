<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * 前端首页控制器
 */
class HomeController extends Controller
{
    /**
     * 显示首页
     *
     * @return View
     */
    public function index(): View
    {
        // 获取系统信息
        $systemInfo = $this->getSystemInfo();

        // 获取核心功能信息
        $coreFeatures = $this->getCoreFeatures();

        // 获取文档列表
        $documentationList = $this->getDocumentationList();

        return view('home.index', compact('systemInfo', 'coreFeatures', 'documentationList'));
    }

    /**
     * 获取核心功能信息
     *
     * @return array
     */
    private function getCoreFeatures(): array
    {
        return [
            [
                'title' => '用户管理',
                'description' => '完整的用户账户管理，支持用户信息维护、状态控制、批量操作等功能',
                'icon' => 'fas fa-users',
                'color' => 'primary',
                'features' => ['用户列表', '用户编辑', '状态管理', '批量操作']
            ],
            [
                'title' => '权限管理',
                'description' => '基于RBAC的权限管理系统，支持角色分配、权限控制、菜单管理等',
                'icon' => 'fas fa-shield-alt',
                'color' => 'success',
                'features' => ['角色管理', '权限分配', '菜单控制', '访问控制']
            ],
            [
                'title' => '系统配置',
                'description' => '灵活的系统配置管理，支持参数设置、字典管理、缓存控制等',
                'icon' => 'fas fa-cogs',
                'color' => 'info',
                'features' => ['参数配置', '字典管理', '缓存管理', '系统设置']
            ],
            [
                'title' => '日志管理',
                'description' => '完整的操作日志记录，支持日志查看、搜索、导出、清理等功能',
                'icon' => 'fas fa-file-alt',
                'color' => 'warning',
                'features' => ['操作日志', '登录日志', '日志搜索', '日志导出']
            ],
            [
                'title' => '组件管理',
                'description' => '模块化组件系统，支持组件安装、卸载、配置、状态管理等',
                'icon' => 'fas fa-puzzle-piece',
                'color' => 'secondary',
                'features' => ['组件安装', '组件配置', '状态管理', '依赖管理']
            ],
            [
                'title' => '文件管理',
                'description' => '强大的文件上传管理，支持多种格式、批量上传、文件预览等',
                'icon' => 'fas fa-folder-open',
                'color' => 'danger',
                'features' => ['文件上传', '文件管理', '图片预览', '批量操作']
            ]
        ];
    }

    /**
     * 获取系统信息
     *
     * @return array
     */
    private function getSystemInfo(): array
    {
        $systemConfig = config('lpadmin.system', []);

        return [
            'name' => $systemConfig['name'] ?? 'LPadmin管理系统',
            'version' => $systemConfig['version'] ?? '1.0.0',
            'description' => $systemConfig['description'] ?? '基于Laravel 10+和PearAdminLayui构建的现代化后台管理系统',
            'logo' => $systemConfig['logo'] ?? '/static/admin/images/logo.png',
            'copyright' => $systemConfig['copyright'] ?? 'LPadmin',
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'contact' => $systemConfig['contact'] ?? [
                'email' => 'jiu-men@qq.com',
                'phone' => '+86 15737185084',
                'address' => '中国·北京',
            ],
            'social' => $systemConfig['social'] ?? [
                'github' => '#',
                'qq' => '446820025',
                'wechat' => 'Baron369',
            ],
            'features' => [
                '现代化架构' => '基于Laravel 10+框架，性能优异',
                '美观界面' => 'PearAdminLayui UI，界面美观',
                '权限系统' => '完整的RBAC权限管理系统',
                '响应式设计' => '支持PC、平板、手机多端访问',
                '高度可配置' => '支持动态配置，灵活调整',
                '安全可靠' => '多层安全防护，操作日志记录',
                '易于扩展' => '模块化设计，支持组件式开发',
                '开源免费' => 'MIT协议，完全开源免费使用',
            ],
            'tech_stack' => [
                '后端框架' => 'Laravel ' . app()->version(),
                'PHP版本' => 'PHP ' . PHP_VERSION,
                '前端框架' => 'PearAdminLayui',
                '数据库' => 'MySQL 8.0+',
                '缓存' => 'Redis 6.0+',
                '架构模式' => 'MVC + Service层',
            ],
        ];
    }

    /**
     * 获取文档列表
     *
     * @return array
     */
    private function getDocumentationList(): array
    {
        return [
            [
                'title' => '项目介绍',
                'description' => '了解LPadmin系统的核心特性、功能模块和技术架构',
                'icon' => 'fas fa-info-circle',
                'file' => 'README.md',
                'type' => '概述',
                'updated' => '2024-09-25'
            ],
            [
                'title' => '快速开始',
                'description' => '5分钟快速上手指南，快速体验系统核心功能',
                'icon' => 'fas fa-rocket',
                'file' => 'QUICKSTART.md',
                'type' => '入门',
                'updated' => '2024-09-25'
            ],
            [
                'title' => '安装指南',
                'description' => '详细的环境要求、安装步骤和配置说明',
                'icon' => 'fas fa-download',
                'file' => 'INSTALL.md',
                'type' => '安装',
                'updated' => '2024-09-25'
            ],
            [
                'title' => '开发文档',
                'description' => '开发规范、代码结构、扩展开发等详细说明',
                'icon' => 'fas fa-code',
                'file' => 'DEVELOPMENT.md',
                'type' => '开发',
                'updated' => '2024-09-25'
            ],
            [
                'title' => 'API接口',
                'description' => '完整的API接口文档，包含请求参数和响应示例',
                'icon' => 'fas fa-plug',
                'file' => 'API.md',
                'type' => '接口',
                'updated' => '2024-09-25'
            ],
            [
                'title' => '部署指南',
                'description' => '生产环境部署、性能优化和运维管理指南',
                'icon' => 'fas fa-server',
                'file' => 'DEPLOYMENT.md',
                'type' => '部署',
                'updated' => '2024-09-25'
            ],
            [
                'title' => '数据库设计',
                'description' => '详细的数据表结构设计和关联关系说明',
                'icon' => 'fas fa-database',
                'file' => 'architecture/database-design.md',
                'type' => '架构',
                'updated' => '2024-09-25'
            ],
            [
                'title' => '权限系统',
                'description' => 'RBAC权限系统的设计原理和使用方法',
                'icon' => 'fas fa-shield-alt',
                'file' => 'architecture/permission-system.md',
                'type' => '架构',
                'updated' => '2024-09-25'
            ],
            [
                'title' => '更新日志',
                'description' => '版本更新记录、新功能介绍和问题修复说明',
                'icon' => 'fas fa-history',
                'file' => 'CHANGELOG.md',
                'type' => '日志',
                'updated' => '2024-09-25'
            ]
        ];
    }

}
