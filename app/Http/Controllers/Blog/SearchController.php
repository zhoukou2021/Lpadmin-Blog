<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog\Post;
use App\Models\Blog\Category;
use App\Models\Blog\Tag;
use App\Models\Blog\Nav;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $lang = $request->attributes->get('current_lang') ?? 'cn';
        $keyword = trim((string) $request->get('q', ''));

        $navs = Nav::query()->where('visible', true)->orderByDesc('sort')->orderBy('id')->get();
        $friendLinks = get_blog_friend_links($lang, 50);

        $processPost = function (Post $post) use ($lang) {
            $title = get_i18n_value($post->title, $lang, '');
            $summary = get_i18n_value($post->summary, $lang, '');
            $categoryName = $post->category ? get_i18n_value($post->category->name, $lang, '') : '';
            $categorySlug = $post->category ? ($post->category->slug ?? ('category_' . $post->category->id)) : '';
            $tags = $post->tags->map(function ($tag) use ($lang) {
                return [
                    'name' => get_i18n_value($tag->name, $lang, ''),
                    'slug' => $tag->slug ?? ('tags_' . $tag->id),
                ];
            })->values();

            return [
                'id' => $post->id,
                'title' => $title,
                'slug' => $post->slug ?? ('article_' . $post->id),
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

        $postsPaginator = null;
        $posts = collect();
        $searchCount = 0;

        if ($keyword !== '') {
            $searchTerm = '%' . $keyword . '%';
            $postsPaginator = Post::where('status', 'published')
                ->where(function ($query) use ($searchTerm) {
                    $query->whereRaw('title->>"$.zh" LIKE ?', [$searchTerm])
                        ->orWhereRaw('title->>"$.en" LIKE ?', [$searchTerm])
                        ->orWhereRaw('title->>"$.cn" LIKE ?', [$searchTerm])
                        ->orWhereRaw('title->>"$.tw" LIKE ?', [$searchTerm])
                        ->orWhereRaw('summary->>"$.zh" LIKE ?', [$searchTerm])
                        ->orWhereRaw('summary->>"$.en" LIKE ?', [$searchTerm])
                        ->orWhereRaw('summary->>"$.cn" LIKE ?', [$searchTerm])
                        ->orWhereRaw('summary->>"$.tw" LIKE ?', [$searchTerm])
                        ->orWhere('slug', 'like', $searchTerm);
                })
                ->with(['category', 'tags'])
                ->withCount(['comments', 'likes', 'favorites'])
                ->orderByDesc('published_at')
                ->paginate(12)
                ->appends(['q' => $keyword]);

            $postsPaginator->getCollection()->transform($processPost);
            $posts = $postsPaginator->getCollection();
            $searchCount = $postsPaginator->total();
        }

        $categoriesRaw = Category::query()->where('visible', true)->orderByDesc('sort')->orderBy('id')->limit(20)->get();
        $categories = $categoriesRaw->map(function ($category) use ($lang) {
            return [
                'id' => $category->id,
                'name' => get_i18n_value($category->name, $lang, ''),
                'slug' => $category->slug ?? ('category_' . $category->id),
            ];
        });

        $tagsRaw = Tag::query()->orderBy('id')->limit(50)->get();
        $tags = $tagsRaw->map(function ($tag) use ($lang) {
            return [
                'id' => $tag->id,
                'name' => get_i18n_value($tag->name, $lang, ''),
                'slug' => $tag->slug ?? ('tags_' . $tag->id),
            ];
        });

        $hotViews = Post::where('status', 'published')
            ->with(['category', 'tags'])
            ->withCount(['comments', 'likes', 'favorites'])
            ->orderByDesc('view_count')
            ->limit(10)
            ->get()
            ->map($processPost);

        $hotFavorites = Post::where('status', 'published')
            ->with(['category', 'tags'])
            ->withCount(['comments', 'likes', 'favorites'])
            ->orderByDesc('favorites_count')
            ->limit(10)
            ->get()
            ->map($processPost);

        $hotComments = Post::where('status', 'published')
            ->with(['category', 'tags'])
            ->withCount(['comments', 'likes', 'favorites'])
            ->orderByDesc('comments_count')
            ->limit(10)
            ->get()
            ->map($processPost);

        $langData = enabled_langs();

        $siteInfo = \App\Helpers\ConfigHelper::getSiteInfo($lang);
        $siteName = $siteInfo['name'] ?? 'Blog';
        $canonicalUrl = url('/search' . ($keyword !== '' ? '?q=' . urlencode($keyword) : ''));
        $seoTitle = $keyword !== ''
            ? __('blog.search_results_for', ['keyword' => $keyword]) . ' - ' . $siteName
            : __('blog.search_results') . ' - ' . $siteName;
        $seoDescription = $keyword !== ''
            ? __('blog.search_results_description', ['keyword' => $keyword, 'count' => $searchCount])
            : ($siteInfo['description'] ?? '');

        $seo = [
            'title' => $seoTitle,
            'keywords' => $siteInfo['keywords'] ?? '',
            'description' => $seoDescription,
            'canonical' => $canonicalUrl,
        ];

        $copyright = $siteInfo['copyright'] ?? ('&copy; ' . date('Y'));

        return view('blog.search.index', compact(
            'lang',
            'navs',
            'keyword',
            'posts',
            'postsPaginator',
            'searchCount',
            'categories',
            'tags',
            'hotViews',
            'hotFavorites',
            'hotComments',
            'langData',
            'friendLinks',
            'siteName',
            'seo',
            'copyright'
        ));
    }
}
