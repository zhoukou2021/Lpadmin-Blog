<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog\Post;
use App\Models\Blog\Favorite;

class FavoriteController extends Controller
{
    public function toggle(Request $request, Post $post)
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent() ?? '';
        $userId = auth()->id() ?? 0;

        // 检查是否已经收藏
        $favoriteQuery = Favorite::where('post_id', $post->id);
        
        if ($userId > 0) {
            // 如果用户已登录，使用user_id判断
            $existingFavorite = $favoriteQuery->where('user_id', $userId)->first();
        } else {
            // 如果用户未登录，使用IP和user_id=0判断
            $existingFavorite = $favoriteQuery->where('user_id', 0)->where('ip', $ip)->first();
        }

        if ($existingFavorite) {
            // 取消收藏
            $existingFavorite->delete();
            $favorited = false;
        } else {
            // 添加收藏
            Favorite::create([
                'post_id' => $post->id,
                'user_id' => $userId,
                'ip' => $ip,
                'ua' => $userAgent,
            ]);
            $favorited = true;
        }

        // 重新获取收藏数
        $favoritesCount = Favorite::where('post_id', $post->id)->count();

        return response()->json([
            'code' => 0,
            'favorited' => $favorited,
            'favorites_count' => $favoritesCount,
            'message' => $favorited ? __('blog.favorite_success') : __('blog.unfavorite_success'),
        ]);
    }
}

