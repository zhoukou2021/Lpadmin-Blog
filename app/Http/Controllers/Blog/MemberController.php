<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LPadmin\User;
use App\Models\Blog\Post;
use App\Models\Blog\Favorite;
use App\Models\Blog\Like;
use App\Models\Blog\Comment;
use App\Models\Blog\Category;
use App\Models\Blog\Tag;
use App\Models\Blog\Ad;

class MemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    /**
     * 会员中心首页
     */
    public function index(Request $request)
    {
        $user = Auth::guard('web')->user();
        $lang = $request->attributes->get('current_lang') ?? 'cn';
        
        // 获取会员中心banner（type=7）
        $memberBanner = get_blog_ad(Ad::TYPE_MEMBER_CENTER, $lang, true);
        
        // 获取统计数据
        $favoritesCount = Favorite::where('user_id', $user->id)->count();
        $likesCount = Like::where('user_id', $user->id)->count();
        $commentsCount = Comment::where('user_id', $user->id)->count();

        // 获取导航、分类、标签等侧边栏数据
        $navs = \App\Models\Blog\Nav::query()->where('visible', true)->orderByDesc('sort')->orderBy('id')->get();
        $friendLinks = get_blog_friend_links($lang, 50);
        $categories = Category::where('visible', true)->orderBy('sort')->get()->map(function($c) use ($lang) {
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

        // 获取版权信息
        $copyright = \App\Helpers\ConfigHelper::getI18n('system_copyright', $lang, '&copy; ' . date('Y'));

        // 语言数据
        $langData = enabled_langs();

        // SEO数据
        $seo = [
            'title' => __('blog.member_center') . ' - ' . __('blog.blog'),
            'description' => __('blog.member_center_description'),
            'canonical' => url('/member'),
        ];

        return view('blog.member.index', compact(
            'lang', 'user', 'navs', 'categories', 'tags', 'friendLinks', 'copyright',
            'favoritesCount', 'likesCount', 'commentsCount', 'memberBanner', 'seo', 'langData'
        ));
    }

    /**
     * 资料编辑
     */
    public function profile(Request $request)
    {
        $user = Auth::guard('web')->user();
        $lang = $request->attributes->get('current_lang') ?? 'cn';
        
        // 获取会员中心banner（type=7）
        $memberBanner = get_blog_ad(Ad::TYPE_MEMBER_CENTER, $lang, true);
        
        // 获取导航、分类、标签等侧边栏数据
        $navs = \App\Models\Blog\Nav::query()->where('visible', true)->orderByDesc('sort')->orderBy('id')->get();
        $friendLinks = get_blog_friend_links($lang, 50);
        $categories = Category::where('visible', true)->orderBy('sort')->get()->map(function($c) use ($lang) {
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

        // 获取版权信息
        $copyright = \App\Helpers\ConfigHelper::getI18n('system_copyright', $lang, '&copy; ' . date('Y'));

        // 语言数据
        $langData = enabled_langs();

        // SEO数据
        $seo = [
            'title' => __('blog.edit_profile') . ' - ' . __('blog.member_center') . ' - ' . __('blog.blog'),
            'description' => __('blog.edit_profile_description'),
            'canonical' => url('/member/profile'),
        ];

        return view('blog.member.profile', compact(
            'lang', 'user', 'navs', 'categories', 'tags', 'friendLinks', 'copyright', 'memberBanner', 'seo', 'langData'
        ));
    }

    /**
     * 更新资料
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::guard('web')->user();
        
        $validated = $request->validate([
            'nickname' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20', 'unique:users,phone,' . $user->id],
            'gender' => ['nullable', 'integer', 'in:0,1,2'],
            'birthday' => ['nullable', 'date'],
            'avatar' => ['nullable', 'string', 'max:255'],
            'avatar_file' => ['nullable', 'image', 'max:2048'], // 最大2MB
        ], [
            'email.email' => '邮箱格式不正确',
            'email.unique' => '邮箱已被使用',
            'phone.unique' => '手机号已被使用',
            'gender.in' => '性别选择无效',
            'birthday.date' => '生日格式不正确',
            'avatar_file.image' => '请上传图片文件',
            'avatar_file.max' => '图片大小不能超过2MB',
        ]);

        // 处理头像文件上传
        if ($request->hasFile('avatar_file')) {
            // 删除旧头像（如果存在）
            if ($user->avatar && strpos($user->avatar, '/storage/') === 0) {
                $oldPath = str_replace('/storage/', '', $user->avatar);
                $oldFullPath = storage_path('app/public/' . $oldPath);
                if (file_exists($oldFullPath)) {
                    @unlink($oldFullPath);
                }
            }
            
            // 保存新头像
            $path = $request->file('avatar_file')->store('uploads/avatars', 'public');
            $validated['avatar'] = '/storage/' . $path;
        }

        // 移除avatar_file字段，只保存avatar路径
        unset($validated['avatar_file']);
        
        $user->fill($validated);
        $user->save();

        return response()->json([
            'code' => 0,
            'message' => '资料更新成功',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'nickname' => $user->nickname,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar_url,
                    'gender' => $user->gender,
                    'birthday' => $user->birthday ? $user->birthday->format('Y-m-d') : null,
                ],
            ],
        ]);
    }

    /**
     * 我的收藏
     */
    public function favorites(Request $request)
    {
        $user = Auth::guard('web')->user();
        $lang = $request->attributes->get('current_lang') ?? 'cn';
        
        // 获取会员中心banner（type=7）
        $memberBanner = get_blog_ad(Ad::TYPE_MEMBER_CENTER, $lang, true);
        
        // 获取收藏的文章
        $favorites = Favorite::where('user_id', $user->id)
            ->with(['post' => function($query) {
                $query->with(['category', 'tags'])->withCount(['comments', 'likes', 'favorites']);
            }])
            ->orderByDesc('created_at')
            ->paginate(12);

        $processPost = function($post) use ($lang) {
            if (!$post) return null;
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

        $posts = collect();
        foreach ($favorites->items() as $favorite) {
            if ($favorite->post) {
                $posts->push($processPost($favorite->post));
            }
        }

        // 获取导航、分类、标签等侧边栏数据
        $navs = \App\Models\Blog\Nav::query()->where('visible', true)->orderByDesc('sort')->orderBy('id')->get();
        $friendLinks = get_blog_friend_links($lang, 50);
        $categories = Category::where('visible', true)->orderBy('sort')->get()->map(function($c) use ($lang) {
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

        // 获取版权信息
        $copyright = \App\Helpers\ConfigHelper::getI18n('system_copyright', $lang, '&copy; ' . date('Y'));

        // 语言数据
        $langData = enabled_langs();

        // SEO数据
        $seo = [
            'title' => __('blog.my_favorites') . ' - ' . __('blog.member_center') . ' - ' . __('blog.blog'),
            'description' => __('blog.my_favorites_description'),
            'canonical' => url('/member/favorites'),
        ];

        return view('blog.member.favorites', compact(
            'lang', 'user', 'navs', 'categories', 'tags', 'friendLinks', 'copyright',
            'posts', 'favorites', 'memberBanner', 'seo', 'langData'
        ));
    }

    /**
     * 我的点赞
     */
    public function likes(Request $request)
    {
        $user = Auth::guard('web')->user();
        $lang = $request->attributes->get('current_lang') ?? 'cn';
        
        // 获取会员中心banner（type=7）
        $memberBanner = get_blog_ad(Ad::TYPE_MEMBER_CENTER, $lang, true);
        
        // 获取点赞的文章
        $likes = Like::where('user_id', $user->id)
            ->with(['post' => function($query) {
                $query->with(['category', 'tags'])->withCount(['comments', 'likes', 'favorites']);
            }])
            ->orderByDesc('created_at')
            ->paginate(12);

        $processPost = function($post) use ($lang) {
            if (!$post) return null;
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

        $posts = collect();
        foreach ($likes->items() as $like) {
            if ($like->post) {
                $posts->push($processPost($like->post));
            }
        }

        // 获取导航、分类、标签等侧边栏数据
        $navs = \App\Models\Blog\Nav::query()->where('visible', true)->orderByDesc('sort')->orderBy('id')->get();
        $friendLinks = get_blog_friend_links($lang, 50);
        $categories = Category::where('visible', true)->orderBy('sort')->get()->map(function($c) use ($lang) {
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

        // 获取版权信息
        $copyright = \App\Helpers\ConfigHelper::getI18n('system_copyright', $lang, '&copy; ' . date('Y'));

        // 语言数据
        $langData = enabled_langs();

        // SEO数据
        $seo = [
            'title' => __('blog.my_likes') . ' - ' . __('blog.member_center') . ' - ' . __('blog.blog'),
            'description' => __('blog.my_likes_description'),
            'canonical' => url('/member/likes'),
        ];

        return view('blog.member.likes', compact(
            'lang', 'user', 'navs', 'categories', 'tags', 'friendLinks', 'copyright',
            'posts', 'likes', 'memberBanner', 'seo', 'langData'
        ));
    }

    /**
     * 我的评价
     */
    public function comments(Request $request)
    {
        $user = Auth::guard('web')->user();
        $lang = $request->attributes->get('current_lang') ?? 'cn';
        
        // 获取会员中心banner（type=7）
        $memberBanner = get_blog_ad(Ad::TYPE_MEMBER_CENTER, $lang, true);
        
        // 获取评论
        $comments = Comment::where('user_id', $user->id)
            ->with(['post'])
            ->orderByDesc('created_at')
            ->paginate(12);

        $processPost = function($post) use ($lang) {
            if (!$post) return null;
            $title = get_i18n_value($post->title, $lang, '');
            $slug = $post->slug ?? ('article_'.$post->id);
            return [
                'id' => $post->id,
                'title' => $title,
                'slug' => $slug,
            ];
        };

        // 获取导航、分类、标签等侧边栏数据
        $navs = \App\Models\Blog\Nav::query()->where('visible', true)->orderByDesc('sort')->orderBy('id')->get();
        $friendLinks = get_blog_friend_links($lang, 50);
        $categories = Category::where('visible', true)->orderBy('sort')->get()->map(function($c) use ($lang) {
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

        // 获取版权信息
        $copyright = \App\Helpers\ConfigHelper::getI18n('system_copyright', $lang, '&copy; ' . date('Y'));

        // 语言数据
        $langData = enabled_langs();

        // SEO数据
        $seo = [
            'title' => __('blog.my_comments') . ' - ' . __('blog.member_center') . ' - ' . __('blog.blog'),
            'description' => __('blog.my_comments_description'),
            'canonical' => url('/member/comments'),
        ];

        return view('blog.member.comments', compact(
            'lang', 'user', 'navs', 'categories', 'tags', 'friendLinks', 'copyright',
            'comments', 'processPost', 'memberBanner', 'seo', 'langData'
        ));
    }

    /**
     * 加载更多收藏（无限滚动）
     */
    public function loadMoreFavorites(Request $request)
    {
        $user = Auth::guard('web')->user();
        $lang = $request->attributes->get('current_lang') ?? 'cn';
        $page = $request->input('page', 2);

        $favorites = Favorite::where('user_id', $user->id)
            ->with(['post' => function($query) {
                $query->with(['category', 'tags'])->withCount(['comments', 'likes', 'favorites']);
            }])
            ->orderByDesc('created_at')
            ->paginate(12, ['*'], 'page', $page);

        $processPost = function($post) use ($lang) {
            if (!$post) return null;
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

        $posts = collect();
        foreach ($favorites->items() as $favorite) {
            if ($favorite->post) {
                $posts->push($processPost($favorite->post));
            }
        }

        return response()->json([
            'code' => 0,
            'data' => $posts,
            'has_more' => $favorites->hasMorePages(),
            'current_page' => $favorites->currentPage(),
            'last_page' => $favorites->lastPage(),
        ]);
    }

    /**
     * 加载更多点赞（无限滚动）
     */
    public function loadMoreLikes(Request $request)
    {
        $user = Auth::guard('web')->user();
        $lang = $request->attributes->get('current_lang') ?? 'cn';
        $page = $request->input('page', 2);

        $likes = Like::where('user_id', $user->id)
            ->with(['post' => function($query) {
                $query->with(['category', 'tags'])->withCount(['comments', 'likes', 'favorites']);
            }])
            ->orderByDesc('created_at')
            ->paginate(12, ['*'], 'page', $page);

        $processPost = function($post) use ($lang) {
            if (!$post) return null;
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

        $posts = collect();
        foreach ($likes->items() as $like) {
            if ($like->post) {
                $posts->push($processPost($like->post));
            }
        }

        return response()->json([
            'code' => 0,
            'data' => $posts,
            'has_more' => $likes->hasMorePages(),
            'current_page' => $likes->currentPage(),
            'last_page' => $likes->lastPage(),
        ]);
    }

    /**
     * 加载更多评论（无限滚动）
     */
    public function loadMoreComments(Request $request)
    {
        $user = Auth::guard('web')->user();
        $lang = $request->attributes->get('current_lang') ?? 'cn';
        $page = $request->input('page', 2);

        $comments = Comment::where('user_id', $user->id)
            ->with(['post'])
            ->orderByDesc('created_at')
            ->paginate(12, ['*'], 'page', $page);

        $processPost = function($post) use ($lang) {
            if (!$post) return null;
            $title = get_i18n_value($post->title, $lang, '');
            $slug = $post->slug ?? ('article_'.$post->id);
            return [
                'id' => $post->id,
                'title' => $title,
                'slug' => $slug,
            ];
        };

        $processedComments = $comments->map(function($comment) use ($processPost) {
            return [
                'id' => $comment->id,
                'content' => $comment->content,
                'status' => $comment->status,
                'created_at' => $comment->created_at->format('Y-m-d H:i'),
                'post' => $processPost($comment->post),
            ];
        });

        return response()->json([
            'code' => 0,
            'data' => $processedComments,
            'has_more' => $comments->hasMorePages(),
            'current_page' => $comments->currentPage(),
            'last_page' => $comments->lastPage(),
        ]);
    }
}

