<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Blog\Post;
use App\Models\Blog\Tag;
use App\Models\Blog\Category;
use App\Models\Blog\Nav;
use App\Models\Blog\Ad;

class BlogDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 分类数据（按树状结构：先创建父分类，再创建子分类）
        $categoriesData = [
            // 一级分类
            [
                'parent_id' => 0,
                'sort' => 100,
                'visible' => true,
                'name' => ['cn' => '技术', 'en' => 'Technology', 'tw' => '科技'],
                'slug' => 'technology',
                'description' => ['cn' => '技术分享', 'en' => 'Tech sharing', 'tw' => '科技分享'],
                'children' => [
                    [
                        'parent_id' => 0, // 将在创建时设置为父分类ID
                        'sort' => 0,
                        'visible' => true,
                        'name' => ['cn' => 'Laravel', 'en' => 'Laravel', 'tw' => 'Laravel'],
                        'slug' => 'laravel',
                        'description' => ['cn' => 'Laravel相关技术、代码、代码分享', 'en' => 'Sharing of Laravel-related technologies, experiences, and code', 'tw' => 'Laravel相關科技、程式碼、程式碼分享'],
                    ],
                    [
                        'parent_id' => 0,
                        'sort' => 0,
                        'visible' => true,
                        'name' => ['cn' => 'PHP', 'en' => 'PHP', 'tw' => 'PHP'],
                        'slug' => 'php',
                        'description' => ['cn' => 'PHP相关技术、代码、知识分享', 'en' => 'Sharing of PHP-related technologies, experiences, and code', 'tw' => 'PHP相關科技、程式碼、知識分享'],
                    ],
                ]
            ],
            [
                'parent_id' => 0,
                'sort' => 0,
                'visible' => true,
                'name' => ['cn' => '源码', 'en' => 'Code', 'tw' => '源碼'],
                'slug' => 'codes',
                'description' => ['cn' => 'PHP/Laravel/ThinkPHP/Mini Program等相关源码分享与下载', 'en' => 'PHP/Laravel/ThinkPHP/Mini Program source code sharing and download', 'tw' => 'PHP/Laravel/ThinkPHP/Mini Program等相關源碼分享與下載'],
            ],
            [
                'parent_id' => 0,
                'sort' => 0,
                'visible' => true,
                'name' => ['cn' => '教程', 'en' => 'Course', 'tw' => '教程'],
                'slug' => 'course',
                'description' => null,
            ],
        ];

        // 创建分类（支持嵌套结构）
        $categories = [];
        foreach ($categoriesData as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);
            
            
            // 先创建分类获取ID
            $category = Category::create([
                'parent_id' => $categoryData['parent_id'],
                'sort' => $categoryData['sort'],
                'visible' => $categoryData['visible'],
                'name' => $categoryData['name'],
                'slug' => $categoryData['slug'] ?? '',
                'description' => $categoryData['description'] ?? null,
            ]);
            
            $categories[] = $category;
            
            // 创建子分类
            if (!empty($children)) {
                foreach ($children as $childData) {
                    $childData['parent_id'] = $category->id;
                    
                    $childCategory = Category::create([
                        'parent_id' => $childData['parent_id'],
                        'sort' => $childData['sort'],
                        'visible' => $childData['visible'],
                        'name' => $childData['name'],
                        'slug' => $childData['slug'] ?? '',
                        'description' => $childData['description'] ?? null,
                    ]);

                    $categories[] = $childCategory;
                }
            }
        }

        // 使用第一个分类作为示例
        $catTech = $categories[0];

        // 标签数据
        $tagsData = [
            ['name' => ['cn' => 'Laravel', 'en' => 'Laravel', 'tw' => 'Laravel']],
            ['name' => ['cn' => 'PHP', 'en' => 'PHP', 'tw' => 'PHP']],
            ['name' => ['cn' => 'Thinkphp', 'en' => 'Thinkphp', 'tw' => 'Thinkphp']],
            ['name' => ['cn' => 'Jquery', 'en' => 'Jquery', 'tw' => 'Jquery']],
            ['name' => ['cn' => 'Mysql', 'en' => 'Mysql', 'tw' => 'Mysql']],
            ['name' => ['cn' => 'Redis', 'en' => 'Redis', 'tw' => 'Redis']],
            ['name' => ['cn' => 'JavaScript', 'en' => 'JavaScript', 'tw' => 'JavaScript']],
            ['name' => ['cn' => 'Vue', 'en' => 'Vue', 'tw' => 'Vue']],
            ['name' => ['cn' => 'React', 'en' => 'React', 'tw' => 'React']],
            ['name' => ['cn' => 'Node.js', 'en' => 'Node.js', 'tw' => 'Node.js']],
            ['name' => ['cn' => 'Webman', 'en' => 'Webman', 'tw' => 'Webman']],
            ['name' => ['cn' => 'Layui', 'en' => 'Layui', 'tw' => 'Layui']],
            ['name' => ['cn' => 'Uniapp', 'en' => 'Uniapp', 'tw' => 'Uniapp']],
            ['name' => ['cn' => '小程序', 'en' => 'Mini Program', 'tw' => '小程序']],
            ['name' => ['cn' => '源码', 'en' => 'Source Code', 'tw' => '源碼']],
            ['name' => ['cn' => '模板', 'en' => 'Template', 'tw' => '模板']],
            ['name' => ['cn' => '代码', 'en' => 'Code', 'tw' => '代碼']],
        ];

        $tags = [];
        foreach ($tagsData as $tagData) {
            // 先创建标签获取ID
            $tag = Tag::create([
                'name' => $tagData['name'],
                'slug' => 'temp_' . time() . '_' . rand(1000, 9999), // 临时slug
            ]);
            
            // 使用 tag-{id} 格式更新 slug
            $tag->slug = 'tag-' . $tag->id;
            $tag->save();
            
            $tags[] = $tag;
        }

        // 使用第一个标签作为示例
        $tagLaravel = $tags[0];

        // 文章
        $post = Post::create([
            'author_id' => 1,
            'status' => 'published',
            'cover' => null,
            'published_at' => now(),
            'title' => ['cn' => '第一篇多语言文章', 'en' => 'First i18n Post'],
            'slug' => 'first-i18n-post', // slug 是字符串类型
            'summary' => ['cn' => '演示多语言 JSON 字段', 'en' => 'Demo for JSON i18n fields'],
            'content' => ['cn' => '正文内容（中文）', 'en' => 'Body content (English)'],
            'meta_title' => ['cn' => '多语言文章 - 演示', 'en' => 'I18n Post - Demo'],
            'meta_desc' => ['cn' => '这是一个多语言文章示例', 'en' => 'An example for multilingual post'],
            'meta_json' => ['category_id' => $catTech->id],
        ]);

        $post->tags()->sync([$tagLaravel->id]);

        // 导航菜单数据（按树状结构：先创建父导航，再创建子导航）
        $navsData = [
            // 一级导航
            [
                'parent_id' => 0,
                'sort' => 100,
                'visible' => true,
                'title' => ['cn' => '首页', 'en' => 'Home', 'tw' => '首頁'],
                'url' => '/',
                'icon' => null,
            ],
            [
                'parent_id' => 0,
                'sort' => 90,
                'visible' => true,
                'title' => ['cn' => '技术', 'en' => 'Technology', 'tw' => '科技'],
                'url' => '/technology',
                'icon' => null,
                'children' => [
                    [
                        'parent_id' => 0, // 将在创建时设置为父导航ID
                        'sort' => 0,
                        'visible' => true,
                        'title' => ['cn' => 'Laravel', 'en' => 'Laravel', 'tw' => 'Laravel'],
                        'url' => '/laravel',
                        'icon' => null,
                    ],
                    [
                        'parent_id' => 0,
                        'sort' => 0,
                        'visible' => true,
                        'title' => ['cn' => 'PHP', 'en' => 'PHP', 'tw' => 'PHP'],
                        'url' => '/php',
                        'icon' => null,
                    ],
                ]
            ],
            [
                'parent_id' => 0,
                'sort' => 80,
                'visible' => true,
                'title' => ['cn' => '源码', 'en' => 'Code', 'tw' => '源碼'],
                'url' => '/codes',
                'icon' => null,
            ],
            [
                'parent_id' => 0,
                'sort' => 70,
                'visible' => true,
                'title' => ['cn' => '教程', 'en' => 'Course', 'tw' => '教程'],
                'url' => '/course',
                'icon' => null,
            ],
            [
                'parent_id' => 0,
                'sort' => 50,
                'visible' => true,
                'title' => ['cn' => '联系我们', 'en' => 'Contact', 'tw' => '聯繫我們'],
                'url' => '/contact',
                'icon' => null,
            ],
        ];

        // 创建导航菜单（支持嵌套结构）
        $navs = [];
        foreach ($navsData as $navData) {
            $children = $navData['children'] ?? [];
            unset($navData['children']);
                        
            // 创建导航
            $nav = Nav::create([
                'parent_id' => $navData['parent_id'],
                'sort' => $navData['sort'],
                'visible' => $navData['visible'],
                'title' => $navData['title'],
                'url' => $navData['url'],
                'icon' => $navData['icon'] ?? null,
            ]);
            
            $navs[] = $nav;
            
            // 创建子导航
            if (!empty($children)) {
                foreach ($children as $childData) {
                    $childData['parent_id'] = $nav->id;
                    
                    $childNav = Nav::create([
                        'parent_id' => $childData['parent_id'],
                        'sort' => $childData['sort'],
                        'visible' => $childData['visible'],
                        'title' => $childData['title'],
                        'url' => $childData['url'],
                        'icon' => $childData['icon'] ?? null,
                    ]);
                    
                    $navs[] = $childNav;
                }
            }
        }

        // 广告数据
        $adsData = [
            // 顶部焦点图 (type=1)
            [
                'type' => Ad::TYPE_TOP_BANNER,
                'title' => ['cn' => 'LPadmin 多语言博客系统', 'en' => 'LPadmin Multi-language Blog System', 'tw' => 'LPadmin 多語言博客系統'],
                'content' => ['cn' => '基于 Laravel 10+ 构建的现代化博客平台，支持多语言、SEO优化、AI文章生成等功能', 'en' => 'Modern blog platform built on Laravel 10+, supporting multi-language, SEO optimization, AI article generation and more', 'tw' => '基於 Laravel 10+ 構建的現代化博客平台，支援多語言、SEO優化、AI文章生成等功能'],
                'link' => '/',
                'image' => '/storage/uploads/ads/UkAqN4zUwpOnsCNDOozDTkFebQ5lgfDIVZcuXQkV.jpg',
                'sort' => 90,
                'visible' => true,
            ],
            // 友情链接 (type=2)
            [
                'type' => Ad::TYPE_FRIENDLY_LINK,
                'title' => ['cn' => 'Laravel 官方文档', 'en' => 'Laravel Documentation', 'tw' => 'Laravel 官方文檔'],
                'content' => null,
                'link' => 'https://laravel.com/docs',
                'image' => null,
                'sort' => 100,
                'visible' => true,
            ],
            [
                'type' => Ad::TYPE_FRIENDLY_LINK,
                'title' => ['cn' => 'PHP 官方', 'en' => 'PHP Official', 'tw' => 'PHP 官方'],
                'content' => null,
                'link' => 'https://www.php.net',
                'image' => null,
                'sort' => 90,
                'visible' => true,
            ],
            // 分类页banner (type=3)
            [
                'type' => Ad::TYPE_CATEGORY_BANNER,
                'title' => ['cn' => '技术分类', 'en' => 'Technology Category', 'tw' => '科技分類'],
                'content' => ['cn' => '探索最新的技术文章', 'en' => 'Explore the latest tech articles', 'tw' => '探索最新的科技文章'],
                'link' => '/category/technology',
                'image' => '/storage/uploads/ads/P5AT3zvNQ4Z2CwzeXupui1fHPZ2PAZzQPfKnt8Ja.jpg',
                'sort' => 100,
                'visible' => true,
            ],
            // 标签页banner (type=4)
            [
                'type' => Ad::TYPE_TAG_BANNER,
                'title' => ['cn' => 'Laravel 标签', 'en' => 'Laravel Tag', 'tw' => 'Laravel 標籤'],
                'content' => ['cn' => '查看所有 Laravel 相关文章', 'en' => 'View all Laravel related articles', 'tw' => '查看所有 Laravel 相關文章'],
                'link' => '/tag/laravel',
                'image' => '/storage/uploads/ads/UkAqN4zUwpOnsCNDOozDTkFebQ5lgfDIVZcuXQkV.jpg',
                'sort' => 100,
                'visible' => true,
            ],
            // 博客详情页banner (type=5)
            [
                'type' => Ad::TYPE_POST_BANNER,
                'title' => ['cn' => '推荐文章', 'en' => 'Recommended Articles', 'tw' => '推薦文章'],
                'content' => ['cn' => '发现更多精彩内容', 'en' => 'Discover more great content', 'tw' => '發現更多精彩內容'],
                'link' => '/',
                'image' => '/storage/uploads/ads/Ie1doVr0jFUTb2yFGrZNkiBeriDehsVCA8OyAx1p.jpg',
                'sort' => 100,
                'visible' => true,
            ],
            // 会员中心banner (type=7)
            [
                'type' => Ad::TYPE_MEMBER_CENTER,
                'title' => ['cn' => '会员中心', 'en' => 'Member Center', 'tw' => '會員中心'],
                'content' => ['cn' => '管理您的个人信息、收藏和评论', 'en' => 'Manage your personal information, favorites and comments', 'tw' => '管理您的個人信息、收藏和評論'],
                'link' => '/member',
                'image' => '/storage/uploads/ads/WpEysia47OTELKj73lQecz6koZUAOOVbflZICJNQ.jpg',
                'sort' => 100,
                'visible' => true,
            ],
        ];

        // 创建广告
        foreach ($adsData as $adData) {
            Ad::create([
                'type' => $adData['type'],
                'title' => $adData['title'],
                'content' => $adData['content'] ?? null,
                'link' => $adData['link'] ?? null,
                'image' => $adData['image'] ?? null,
                'sort' => $adData['sort'],
                'visible' => $adData['visible'],
            ]);
        }
    }
}


