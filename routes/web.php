<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return redirect('/zh');
// });

// 先加载后台路由，避免被前台规则拦截
require __DIR__.'/lpadmin.php';

// 博客路由：统一无语言前缀，通过cookie/session保存语言设置
Route::group(['middleware' => ['set.locale']], function () {
    Route::get('/', [\App\Http\Controllers\Blog\HomeController::class, 'index'])->name('site.home');
    Route::get('/contact', [\App\Http\Controllers\Blog\ContactController::class, 'show'])->name('site.contact.show');
    Route::get('/sitemap', [\App\Http\Controllers\Blog\SitemapController::class, 'index'])->name('site.sitemap');
    
    // 会员中心路由（需要认证，必须在 /{slug} 之前定义以避免被捕获）
    Route::group(['middleware' => ['auth:web'], 'prefix' => 'member'], function () {
        Route::get('/', [\App\Http\Controllers\Blog\MemberController::class, 'index'])->name('site.member.index');
        Route::get('/profile', [\App\Http\Controllers\Blog\MemberController::class, 'profile'])->name('site.member.profile');
        Route::post('/profile', [\App\Http\Controllers\Blog\MemberController::class, 'updateProfile'])->name('site.member.updateProfile');
        Route::get('/favorites', [\App\Http\Controllers\Blog\MemberController::class, 'favorites'])->name('site.member.favorites');
        Route::get('/favorites/load-more', [\App\Http\Controllers\Blog\MemberController::class, 'loadMoreFavorites'])->name('site.member.favorites.loadMore');
        Route::get('/likes', [\App\Http\Controllers\Blog\MemberController::class, 'likes'])->name('site.member.likes');
        Route::get('/likes/load-more', [\App\Http\Controllers\Blog\MemberController::class, 'loadMoreLikes'])->name('site.member.likes.loadMore');
        Route::get('/comments', [\App\Http\Controllers\Blog\MemberController::class, 'comments'])->name('site.member.comments');
        Route::get('/comments/load-more', [\App\Http\Controllers\Blog\MemberController::class, 'loadMoreComments'])->name('site.member.comments.loadMore');
    });
    
    Route::get('/category/{slug}/load-more', [\App\Http\Controllers\Blog\CategoryController::class, 'loadMore'])->name('site.category.loadMore');
    Route::get('/tag/{slug}/load-more', [\App\Http\Controllers\Blog\TagController::class, 'loadMore'])->name('site.tag.loadMore');
    Route::get('/{slug}', [\App\Http\Controllers\Blog\PostController::class, 'show'])->name('site.post.show');
});

// 语言切换路由
Route::post('/switch-lang', [\App\Http\Controllers\Blog\LanguageController::class, 'switch'])->name('site.language.switch');

// 评论提交路由（需要登录，也需要语言中间件）
Route::post('/posts/{post}/comments', [\App\Http\Controllers\Blog\CommentController::class, 'store'])->middleware(['set.locale', 'auth:web'])->name('site.comment.store');

// 点赞路由
Route::post('/posts/{post}/like', [\App\Http\Controllers\Blog\LikeController::class, 'toggle'])->name('site.post.like');

// 收藏路由
Route::post('/posts/{post}/favorite', [\App\Http\Controllers\Blog\FavoriteController::class, 'toggle'])->name('site.post.favorite');

// 用户认证路由
Route::post('/auth/login', [\App\Http\Controllers\Blog\AuthController::class, 'login'])->name('site.auth.login');
Route::post('/auth/register', [\App\Http\Controllers\Blog\AuthController::class, 'register'])->name('site.auth.register');
Route::post('/auth/logout', [\App\Http\Controllers\Blog\AuthController::class, 'logout'])->name('site.auth.logout');
Route::get('/auth/user', [\App\Http\Controllers\Blog\AuthController::class, 'user'])->name('site.auth.user');

