<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog\Category;
use App\Models\Blog\Post;
use App\Models\Blog\Tag;
use App\Models\Blog\Ad;

class CategoryController extends Controller
{
    public function show(Request $request, string $slug)
    {
        // 从中间件获取语言代码
        $lang = $request->attributes->get('current_lang') ?? 'cn';

        // 查询分类，支持 category_{id} 回退
        $category = Category::where('slug', $slug)->where('visible', true)->first();
        if (!$category && preg_match('/^category_(\d+)$/i', $slug, $m)) {
            $category = Category::where('id', (int)$m[1])->where('visible', true)->first();
        }
        abort_if(!$category, 404);

        // 获取分类名称和描述
        $categoryName = get_i18n_value($category->name, $lang, '');
        $categoryDescription = get_i18n_value($category->description, $lang, '');

        // 顶部导航
        $navs = \App\Models\Blog\Nav::query()->where('visible', true)->orderByDesc('sort')->orderBy('id')->get();

        // 分类页banner广告（type=3）
        $categoryBanner = get_blog_ad(Ad::TYPE_CATEGORY_BANNER, $lang, true);

        // 友情链接
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

        // 获取当前分类及其所有子分类的ID
        $categoryIds = [$category->id];
        $getChildCategoryIds = function($parentId) use (&$getChildCategoryIds, &$categoryIds) {
            $children = Category::where('parent_id', $parentId)->where('visible', true)->pluck('id')->toArray();
            foreach ($children as $childId) {
                $categoryIds[] = $childId;
                $getChildCategoryIds($childId); // 递归获取子分类的子分类
            }
        };
        $getChildCategoryIds($category->id);

        // 获取该分类及其子分类下的文章（分页）
        $postsRaw = Post::where('status', 'published')
            ->whereIn('category_id', $categoryIds)
            ->with(['category', 'tags'])
            ->withCount(['comments', 'likes', 'favorites'])
            ->orderByDesc('published_at')
            ->paginate(12);

        $posts = $postsRaw->map($processPost);

        // 右侧边栏数据
        $categoriesRaw = Category::query()->where('visible', true)->orderByDesc('sort')->orderBy('id')->limit(20)->get();
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

        $hotViewsRaw = Post::where('status', 'published')
            ->with(['category', 'tags'])
            ->withCount(['comments', 'likes', 'favorites'])
            ->orderByDesc('view_count')
            ->limit(10)
            ->get();
        $hotViews = $hotViewsRaw->map($processPost);

        $hotFavoritesRaw = Post::where('status', 'published')
            ->with(['category', 'tags'])
            ->withCount(['comments', 'likes', 'favorites'])
            ->orderByDesc('favorites_count')
            ->limit(10)
            ->get();
        $hotFavorites = $hotFavoritesRaw->map($processPost);

        // 推荐文章（推荐的文章，不属于当前分类）
        $recommendedRaw = Post::where('status', 'published')
            ->where('recommend', 1)
            ->where('category_id', '!=', $category->id)
            ->with(['category', 'tags'])
            ->withCount(['comments', 'likes', 'favorites'])
            ->orderByDesc('published_at')
            ->limit(10)
            ->get();
        $recommended = $recommendedRaw->map($processPost);

        // 热门评论（按评论数降序排列的博客文章）
        $hotCommentsRaw = Post::where('status', 'published')
            ->with(['category', 'tags'])
            ->withCount(['comments', 'likes', 'favorites'])
            ->orderByDesc('comments_count')
            ->limit(10)
            ->get();
        $hotComments = $hotCommentsRaw->map($processPost);

        $langData = enabled_langs();

        // 获取版权信息
        $copyright = \App\Helpers\ConfigHelper::getI18n('system_copyright', $lang, '&copy; ' . date('Y'));

        // SEO信息
        $finalSlug = $category->slug ?? ('category_'.$category->id);
        $canonicalUrl = url("/{$finalSlug}");
        $seo = [
            'title' => $categoryName . ' - ' . __('blog.category'). ' - ' .\App\Helpers\ConfigHelper::getI18n('site_name', $lang, ''),
            'keywords' => \App\Helpers\ConfigHelper::getI18n('site_keywords', $lang, ''),
            'description' => $categoryDescription ?? \App\Helpers\ConfigHelper::getI18n('site_description', $lang, ''),
            'canonical' => $canonicalUrl,
        ];

        return view('blog.category.show', compact(
            'lang', 'navs', 'category', 'categoryName', 'categoryDescription',
            'categoryBanner', 'posts', 'postsRaw', 'categories', 'tags',
            'hotViews', 'hotFavorites', 'recommended', 'hotComments',
            'langData', 'friendLinks', 'copyright', 'canonicalUrl', 'seo'
        ));
    }

    public function loadMore(Request $request, string $slug)
    {
        $lang = $request->attributes->get('current_lang') ?? 'cn';
        $page = $request->input('page', 2);

        $category = Category::where('slug', $slug)->where('visible', true)->first();
        if (!$category && preg_match('/^category_(\d+)$/i', $slug, $m)) {
            $category = Category::where('id', (int)$m[1])->where('visible', true)->first();
        }
        if (!$category) {
            return response()->json(['code' => 404, 'message' => '分类不存在']);
        }

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

        // 获取当前分类及其所有子分类的ID
        $categoryIds = [$category->id];
        $getChildCategoryIds = function($parentId) use (&$getChildCategoryIds, &$categoryIds) {
            $children = Category::where('parent_id', $parentId)->where('visible', true)->pluck('id')->toArray();
            foreach ($children as $childId) {
                $categoryIds[] = $childId;
                $getChildCategoryIds($childId);
            }
        };
        $getChildCategoryIds($category->id);

        // 获取该分类及其子分类下的文章（分页）
        $postsRaw = Post::where('status', 'published')
            ->whereIn('category_id', $categoryIds)
            ->with(['category', 'tags'])
            ->withCount(['comments', 'likes', 'favorites'])
            ->orderByDesc('published_at')
            ->paginate(12, ['*'], 'page', $page);

        $posts = $postsRaw->map($processPost);

        return response()->json([
            'code' => 0,
            'data' => $posts,
            'has_more' => $postsRaw->hasMorePages(),
            'current_page' => $postsRaw->currentPage(),
            'last_page' => $postsRaw->lastPage(),
        ]);
    }
}

