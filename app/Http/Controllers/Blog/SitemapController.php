<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog\Post;
use App\Models\Blog\Category;
use App\Models\Blog\Tag;

class SitemapController extends Controller
{
    public function index(Request $request)
    {
        // 从中间件获取语言代码
        $lang = $request->attributes->get('current_lang') ?? 'cn';

        // 顶部导航
        $navs = \App\Models\Blog\Nav::query()->where('visible', true)->orderByDesc('sort')->orderBy('id')->get();
        $navTree = $navs->groupBy('parent_id');

        // 友情链接
        $friendLinks = get_blog_friend_links($lang, 50);

        // 获取所有已发布的文章
        $posts = Post::where('status', 'published')
            ->with(['category', 'tags'])
            ->orderByDesc('published_at')
            ->get()
            ->map(function($post) use ($lang) {
                return [
                    'id' => $post->id,
                    'title' => get_i18n_value($post->title, $lang, ''),
                    'slug' => $post->slug ?? ('article_'.$post->id),
                    'url' => blog_url($post->slug ?? ('article_'.$post->id)),
                    'published_at' => $post->published_at,
                    'category_name' => $post->category ? get_i18n_value($post->category->name, $lang, '') : '',
                    'category_slug' => $post->category ? ($post->category->slug ?? ('category_'.$post->category->id)) : '',
                ];
            });

        // 获取所有分类
        $categories = Category::where('visible', true)
            ->orderByDesc('sort')
            ->orderBy('id')
            ->get()
            ->map(function($category) use ($lang) {
                return [
                    'id' => $category->id,
                    'name' => get_i18n_value($category->name, $lang, ''),
                    'slug' => $category->slug ?? ('category_'.$category->id),
                    'url' => blog_url($category->slug ?? ('category_'.$category->id)),
                    'description' => get_i18n_value($category->description, $lang, ''),
                ];
            });

        // 获取所有标签
        $tags = Tag::orderBy('id')
            ->get()
            ->map(function($tag) use ($lang) {
                return [
                    'id' => $tag->id,
                    'name' => get_i18n_value($tag->name, $lang, ''),
                    'slug' => $tag->slug ?? ('tags_'.$tag->id),
                    'url' => blog_url($tag->slug ?? ('tags_'.$tag->id)),
                ];
            });

        $langData = enabled_langs();

        // 获取网站基本信息
        $siteInfo = \App\Helpers\ConfigHelper::getSiteInfo($lang);
        $siteName = $siteInfo['name'];
        $copyright = $siteInfo['copyright'];

        // SEO信息
        $seo = [
            'title' => __('blog.sitemap_title') . ' - ' . $siteName,
            'keywords' => $siteInfo['keywords'],
            'description' => __('blog.sitemap_title') . ' - ' . $siteInfo['description'],
            'canonical' => url('/sitemap'),
        ];

        return view('blog.sitemap.index', compact(
            'lang', 'navs', 'navTree', 'friendLinks', 'posts', 'categories', 
            'tags', 'langData', 'siteName', 'copyright', 'seo'
        ));
    }
}

