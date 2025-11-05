<?php

namespace App\Http\Controllers\LPadmin\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Blog\Comment;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $comments = Comment::query()
            ->orderByDesc('id')
            ->paginate(30);
        return view('lpadmin.blog.comment.index', compact('comments'));
    }

    public function select(Request $request)
    {
        $query = Comment::query()->with(['post', 'user'])->orderByDesc('id');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('post_id')) {
            $query->where('post_id', (int)$request->post_id);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', (int)$request->user_id);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where('content', 'like', "%$q%");
        }
        $list = $query->paginate($request->get('limit', 20));
        $data = collect($list->items())->map(function(Comment $c){
            // 获取文章标题（多语言，默认使用中文）
            $postTitle = '未知文章';
            if ($c->post) {
                $postTitle = get_i18n_value($c->post->title ?? [], 'zh', '未知文章');
            }
            
            // 获取用户名
            $username = '未知用户';
            if ($c->user) {
                $username = $c->user->nickname ?: $c->user->username;
            }
            
            return [
                'id' => $c->id,
                'post_id' => $c->post_id,
                'post_title' => $postTitle,
                'user_id' => $c->user_id,
                'username' => $username,
                'content' => $c->content,
                'status' => $c->status,
                'created_at' => (string)$c->created_at,
            ];
        })->toArray();

        return response()->json([
            'code' => 0,
            'message' => 'success',
            'count' => $list->total(),
            'data' => $data,
        ]);
    }

    public function approve(Request $request, Comment $comment): JsonResponse
    {
        try {
            $comment->update(['status' => 'approved']);
            return response()->json([
                'code' => 0,
                'message' => '已通过',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '操作失败: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function block(Request $request, Comment $comment): JsonResponse
    {
        try {
            $comment->update(['status' => 'blocked']);
            return response()->json([
                'code' => 0,
                'message' => '已屏蔽',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '操作失败: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, Comment $comment): JsonResponse
    {
        try {
            $comment->delete();
            return response()->json([
                'code' => 0,
                'message' => '已删除',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '删除失败: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function batchDestroy(Request $request): JsonResponse
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return response()->json([
                'code' => 400,
                'message' => '参数错误'
            ]);
        }
        
        try {
            Comment::whereIn('id', $ids)->delete();
            return response()->json([
                'code' => 0,
                'message' => '批量删除成功'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '批量删除失败: ' . $e->getMessage(),
            ], 500);
        }
    }
}


