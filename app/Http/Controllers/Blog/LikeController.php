<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog\Post;
use App\Models\Blog\Like;

class LikeController extends Controller
{
    public function toggle(Request $request, Post $post)
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent() ?? '';
        $userId = auth()->id() ?? 0;

        // 检查是否已经点赞
        $likeQuery = Like::where('post_id', $post->id);
        
        if ($userId > 0) {
            // 如果用户已登录，使用user_id判断
            $existingLike = $likeQuery->where('user_id', $userId)->first();
        } else {
            // 如果用户未登录，使用IP和user_id=0判断
            $existingLike = $likeQuery->where('user_id', 0)->where('ip', $ip)->first();
        }

        if ($existingLike) {
            // 取消点赞
            $existingLike->delete();
            $liked = false;
        } else {
            // 添加点赞
            Like::create([
                'post_id' => $post->id,
                'user_id' => $userId,
                'ip' => $ip,
                'ua' => $userAgent,
            ]);
            $liked = true;
        }

        // 重新获取点赞数
        $likesCount = Like::where('post_id', $post->id)->count();

        return response()->json([
            'code' => 0,
            'liked' => $liked,
            'likes_count' => $likesCount,
            'message' => $liked ? __('blog.like_success') : __('blog.unlike_success'),
        ]);
    }
}

