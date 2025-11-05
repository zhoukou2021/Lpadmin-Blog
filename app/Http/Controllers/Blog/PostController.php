<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog\Post;
use App\Models\Blog\Category;
use App\Models\Blog\Tag;
use App\Models\Blog\Ad;
use App\Models\Blog\Comment;

class PostController extends Controller
{
    public function show(Request $request, string $slug)
    {
        // 从请求属性获取当前语言（由中间件设置）
        $lang = $request->attributes->get('current_lang') ?? 'cn';
        
        // 优先检查是否为分类
        $category = Category::where('slug', $slug)->where('visible', true)->first();
        if (!$category && preg_match('/^category_(\d+)$/i', $slug, $m)) {
            $category = Category::where('id', (int)$m[1])->where('visible', true)->first();
        }
        if ($category) {
            // 路由到分类控制器
            return app(\App\Http\Controllers\Blog\CategoryController::class)->show($request, $slug);
        }
        
        // 检查是否为标签
        $tag = Tag::where('slug', $slug)->first();
        if (!$tag && preg_match('/^tags_(\d+)$/i', $slug, $m)) {
            $tag = Tag::where('id', (int)$m[1])->first();
        }
        if ($tag) {
            return app(\App\Http\Controllers\Blog\TagController::class)->show($request, $slug);
        }
        
        // 按slug查询文章，不存在则回退 article_{id}
        $post = Post::where('slug', $slug)->where('status','published')->first();
        if (!$post && preg_match('/^article_(\d+)$/i', $slug, $m)) {
            $post = Post::where('id', (int)$m[1])->where('status','published')->first();
        }
        abort_if(!$post, 404);
        
        // 增加浏览量
        $post->increment('view_count');
        $post->refresh();

        // 顶部导航
        $navs = \App\Models\Blog\Nav::query()->where('visible', true)->orderByDesc('sort')->orderBy('id')->get();

        // 文章详情页banner广告（type=5）
        $postBanner = get_blog_ad(Ad::TYPE_POST_BANNER, $lang, true);

        // 友情链接
        $friendLinks = get_blog_friend_links($lang, 50);

        // 获取文章多语言数据
        $postTitle = get_i18n_value($post->title, $lang, '');
        $postContent = get_i18n_value($post->content, $lang, '');
        $postSummary = get_i18n_value($post->summary, $lang, '');
        $categoryName = $post->category ? get_i18n_value($post->category->name, $lang, '') : '';
        $categorySlug = $post->category ? ($post->category->slug ?? ('category_'.$post->category->id)) : '';
        $postTags = $post->tags->map(function($tag) use ($lang) {
            return [
                'id' => $tag->id,
                'name' => get_i18n_value($tag->name, $lang, ''),
                'slug' => $tag->slug ?? ('tags_'.$tag->id),
            ];
        });

        // 获取关联数据
        $post->load(['category', 'tags']);
        $post->loadCount([
            'comments as comments_count' => function($q) {
                $q->where('status', 'approved');
            },
            'likes',
            'favorites'
        ]);

        // 检查当前用户/IP是否已点赞
        $userId = auth()->id() ?? 0;
        $ip = $request->ip();
        $isLiked = false;
        if ($userId > 0) {
            $isLiked = \App\Models\Blog\Like::where('post_id', $post->id)
                ->where('user_id', $userId)
                ->exists();
        } else {
            $isLiked = \App\Models\Blog\Like::where('post_id', $post->id)
                ->where('user_id', 0)
                ->where('ip', $ip)
                ->exists();
        }

        // 检查当前用户/IP是否已收藏
        $isFavorited = false;
        if ($userId > 0) {
            $isFavorited = \App\Models\Blog\Favorite::where('post_id', $post->id)
                ->where('user_id', $userId)
                ->exists();
        } else {
            $isFavorited = \App\Models\Blog\Favorite::where('post_id', $post->id)
                ->where('user_id', 0)
                ->where('ip', $ip)
                ->exists();
        }

        // 获取当前文章的评论（已审核通过的）
        $commentsRaw = Comment::where('post_id', $post->id)
            ->where('status', 'approved')
            ->with(['user' => function($q) {
                $q->select('id', 'nickname', 'avatar');
            }])
            ->orderBy('created_at', 'asc')
            ->get();
        
        // 构建评论树结构
        $buildCommentTree = function($comments) {
            $tree = [];
            $indexed = [];
            
            // 先创建索引
            foreach ($comments as $comment) {
                $indexed[$comment->id] = [
                    'id' => $comment->id,
                    'parent_id' => $comment->parent_id ?? 0,
                    'content' => $comment->content,
                    'nickname' => $comment->user ? ($comment->user->nickname ?? '匿名') : '匿名',
                    'avatar' => $comment->user ? ($comment->user->avatar ?? '') : '',
                    'created_at' => $comment->created_at->format('Y-m-d H:i'),
                    'ip' => $comment->ip ?? '',
                    'children' => [],
                ];
            }
            
            // 构建树结构
            foreach ($indexed as $id => $comment) {
                if ($comment['parent_id'] == 0) {
                    $tree[] = &$indexed[$id];
                } else {
                    if (isset($indexed[$comment['parent_id']])) {
                        $indexed[$comment['parent_id']]['children'][] = &$indexed[$id];
                    }
                }
            }
            
            return $tree;
        };
        
        $comments = $buildCommentTree($commentsRaw);

        // 处理文章数据的辅助函数（用于相关文章）
        $processPost = function($p) use ($lang) {
            $title = get_i18n_value($p->title, $lang, '');
            $slug = $p->slug ?? ('article_'.$p->id);
            $summary = get_i18n_value($p->summary, $lang, '');
            $categoryName = $p->category ? get_i18n_value($p->category->name, $lang, '') : '';
            $categorySlug = $p->category ? ($p->category->slug ?? ('category_'.$p->category->id)) : '';
            $tags = $p->tags->map(function($tag) use ($lang) {
                return [
                    'name' => get_i18n_value($tag->name, $lang, ''),
                    'slug' => $tag->slug ?? ('tags_'.$tag->id),
                ];
            });
            return [
                'id' => $p->id,
                'title' => $title,
                'slug' => $slug,
                'summary' => $summary,
                'cover' => $p->cover ?? '',
                'published_at' => $p->published_at,
                'category_name' => $categoryName,
                'category_slug' => $categorySlug,
                'tags' => $tags,
                'view_count' => $p->view_count ?? 0,
                'comments_count' => $p->comments_count ?? 0,
                'likes_count' => $p->likes_count ?? 0,
                'favorites_count' => $p->favorites_count ?? 0,
            ];
        };

        // 上一篇和下一篇文章（同分类，按发布时间排序）
        $prevPost = null;
        $nextPost = null;
        if ($post->category_id) {
            // 上一篇（发布时间更早的文章）
            $prevPostRaw = Post::where('status', 'published')
                ->where('category_id', $post->category_id)
                ->where('published_at', '<', $post->published_at ?? $post->created_at)
                ->with(['category', 'tags'])
                ->orderByDesc('published_at')
                ->first();
            if ($prevPostRaw) {
                $prevPost = $processPost($prevPostRaw);
            }
            
            // 下一篇（发布时间更晚的文章）
            $nextPostRaw = Post::where('status', 'published')
                ->where('category_id', $post->category_id)
                ->where('published_at', '>', $post->published_at ?? $post->created_at)
                ->with(['category', 'tags'])
                ->orderBy('published_at')
                ->first();
            if ($nextPostRaw) {
                $nextPost = $processPost($nextPostRaw);
            }
        }

        // 相关文章（同分类的其他文章）
        $relatedPosts = Post::where('status', 'published')
            ->where('id', '!=', $post->id);
        if ($post->category_id) {
            $relatedPosts->where('category_id', $post->category_id);
        } else {
            // 如果没有分类，则按标签相关
            if ($post->tags->count() > 0) {
                $tagIds = $post->tags->pluck('id')->toArray();
                $relatedPosts->whereHas('tags', function($q) use ($tagIds) {
                    $q->whereIn('blog_tags.id', $tagIds);
                });
            } else {
                // 如果既没有分类也没有标签，则显示最新的文章
                $relatedPosts->orderByDesc('published_at');
            }
        }
        $relatedPosts = $relatedPosts->with(['category', 'tags'])
            ->withCount(['comments', 'likes', 'favorites'])
            ->orderByDesc('published_at')
            ->limit(6)
            ->get()
            ->map($processPost);

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

        // 推荐文章（推荐的其他文章）
        $recommendedRaw = Post::where('status', 'published')
            ->where('recommend', 1)
            ->where('id', '!=', $post->id)
            ->with(['category', 'tags'])
            ->withCount(['comments', 'likes', 'favorites'])
            ->orderByDesc('published_at')
            ->limit(10)
            ->get();
        $recommended = $recommendedRaw->map($processPost);

        // 热门评论（按评论数降序排列的博客文章）
        $hotCommentsRaw = Post::where('status', 'published')
            ->where('id', '!=', $post->id)
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
        $finalSlug = $post->slug ?? ('article_'.$post->id);
        $canonicalUrl = url("/{$finalSlug}");
        $seo = [
            'title' => $postTitle . ' - ' . __('blog.blog'). ' - ' .\App\Helpers\ConfigHelper::getI18n('site_name', $lang, ''),
            'keywords' => \App\Helpers\ConfigHelper::getI18n('site_keywords', $lang, ''),
            'description' => $postSummary ?? \App\Helpers\ConfigHelper::getI18n('site_description', $lang, ''),
            'canonical' => $canonicalUrl,
        ];

        return view('blog.post.show', compact(
            'lang', 'navs', 'post', 'postTitle', 'postContent', 'postSummary',
            'postBanner', 'categoryName', 'categorySlug', 'postTags',
            'prevPost', 'nextPost', 'relatedPosts', 'comments',
            'categories', 'tags', 'hotViews', 'hotFavorites', 'recommended',
            'hotComments', 'langData', 'friendLinks', 'copyright', 'canonicalUrl', 'seo', 'isLiked', 'isFavorited'
        ));
    }
}


