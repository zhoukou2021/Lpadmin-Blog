<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog\Post;
use App\Models\Blog\Category;
use App\Models\Blog\Tag;
use App\Models\Blog\Ad;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // 从中间件获取语言代码（由SetLocaleFromPath中间件从cookie读取并设置）
        $lang = $request->attributes->get('current_lang') ?? 'cn';

        // 顶部导航
        $navs = \App\Models\Blog\Nav::query()->where('visible', true)->orderByDesc('sort')->orderBy('id')->get();
        $navTree = $navs->groupBy('parent_id');

        // 焦点图广告
        $banners = get_blog_ad(Ad::TYPE_TOP_BANNER, $lang, false, 8);
        $friendLinks = get_blog_friend_links($lang, 50);

        // 处理文章数据的辅助函数
        $processPost = function($post) use ($lang) {
            $title = get_i18n_value($post->title, $lang, '');
            $slug = $post->slug ?? ('article_'.$post->id);
            $summary = get_i18n_value($post->summary, $lang, '');
            $categoryName = $post->category ? get_i18n_value($post->category->name, $lang, '') : '';
            $categorySlug = $post->category ? ($post->category->slug ?? ('category_'.$post->category->id)) : '';
            $tags = $post->tags->map(function($tag) use ($lang) {
                return [
                    'name' => get_i18n_value($tag->name, $lang, ''),
                    'slug' => $tag->slug ?? ('tags_'.$tag->id),
                ];
            });
            return [
                'id' => $post->id,
                'title' => $title,
                'slug' => $slug,
                'summary' => $summary,
                'cover' => $post->cover ?? '',
                'published_at' => $post->published_at,
                'category_name' => $categoryName,
                'category_slug' => $categorySlug,
                'tags' => $tags,
                'view_count' => $post->view_count ?? 0,
                'comments_count' => $post->comments_count ?? 0,
                'likes_count' => $post->likes_count ?? 0,
                'favorites_count' => $post->favorites_count ?? 0,
            ];
        };

        // 主体左侧：推荐、最新、热门（点赞降序）
        $recommendedRaw = Post::where('status','published')
            ->where('recommend',1)
            ->with(['category', 'tags'])
            ->withCount(['comments', 'likes', 'favorites'])
            ->orderByDesc('published_at')
            ->limit(10)
            ->get();
        $recommended = $recommendedRaw->map($processPost);
        
        $latestRaw = Post::where('status','published')
            ->with(['category', 'tags'])
            ->withCount(['comments', 'likes', 'favorites'])
            ->orderByDesc('published_at')
            ->limit(10)
            ->get();
        $latest = $latestRaw->map($processPost);
        
        $hotRaw = Post::where('status','published')
            ->with(['category', 'tags'])
            ->withCount(['comments', 'likes', 'favorites'])
            ->orderByDesc('likes_count')
            ->limit(10)
            ->get();
        $hot = $hotRaw->map($processPost);

        // 右侧：分类、标签云、热门评论（按创建倒序前10，示例）、热门查看（暂无浏览量字段，暂以 likes_count 代替）、热门收藏
        $categoriesRaw = Category::query()->where('visible',true)->orderByDesc('sort')->orderBy('id')->limit(20)->get();
        $categories = $categoriesRaw->map(function($c) use ($lang) {
            return [
                'id' => $c->id,
                'name' => get_i18n_value($c->name, $lang, ''),
                'slug' => $c->slug ?? ('category_'.$c->id),
            ];
        });
        
        $tagsRaw = Tag::query()->orderBy('id')->limit(50)->get();
        $tags = $tagsRaw->map(function($t) use ($lang) {
            return [
                'id' => $t->id,
                'name' => get_i18n_value($t->name, $lang, ''),
                'slug' => $t->slug ?? ('tags_'.$t->id),
            ];
        });
        
        // 热门评论（按评论数降序排列的博客文章）
        $hotCommentsRaw = Post::where('status', 'published')
            ->with(['category', 'tags'])
            ->withCount(['comments', 'likes', 'favorites'])
            ->orderByDesc('comments_count')
            ->limit(10)
            ->get();
        $hotComments = $hotCommentsRaw->map($processPost);
        
        $hotViewsRaw = Post::where('status','published')
            ->with(['category', 'tags'])
            ->withCount(['comments', 'likes', 'favorites'])
            ->orderByDesc('likes_count')
            ->limit(10)
            ->get();
        $hotViews = $hotViewsRaw->map($processPost);
        
        $hotFavoritesRaw = Post::where('status','published')
            ->with(['category', 'tags'])
            ->withCount(['comments', 'likes', 'favorites'])
            ->orderByDesc('favorites_count')
            ->limit(10)
            ->get();
        $hotFavorites = $hotFavoritesRaw->map($processPost);

        $langData = enabled_langs();

        // 获取网站基本信息（支持多语言）
        $siteInfo = \App\Helpers\ConfigHelper::getSiteInfo($lang);
        $siteName = $siteInfo['name'];
        $copyright = $siteInfo['copyright'];
        $siteTitle = $siteInfo['title'];
        $siteKeywords = $siteInfo['keywords'];
        $siteDescription = $siteInfo['description'];

        // 构建SEO信息
        $seoTitle = $siteName;
        if (!empty($siteTitle)) {
            $seoTitle = $siteName . ' - ' . $siteTitle;
        }
        
        $seo = [
            'title' => $seoTitle,
            'keywords' => $siteKeywords,
            'description' => $siteDescription,
        ];

        return view('blog.home.index', compact(
            'lang','navs','navTree','banners','recommended','latest','hot',
            'categories','tags','hotComments','hotViews','hotFavorites','langData','friendLinks','siteName','copyright','seo'
        ));
    }
}


