<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Blog\Comment;
use App\Models\Blog\Post;
use App\Services\CommentFilterService;
use App\Helpers\ConfigHelper;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        // 验证用户是否已登录
        if (!auth()->check()) {
            // 获取当前语言并设置语言环境
            // 优先从request attributes获取（由中间件设置），如果没有则直接从cookie读取
            $lang = $request->attributes->get('current_lang');
            if (!$lang) {
                // 直接从cookie读取（因为AJAX请求可能中间件没有设置attributes）
                $lang = $request->cookie('locale', 'cn');
            }
            // 语言代码映射：cookie中的语言代码 -> Laravel locale（与中间件保持一致）
            $langMap = ['cn' => 'zh', 'en' => 'en', 'tw' => 'tw'];
            $locale = $langMap[$lang] ?? 'zh';
            app()->setLocale($locale);
            
            // 使用翻译函数获取错误消息
            $translationKey = 'blog.comment_login_required';
            $message = trans($translationKey, [], $locale);
            
            // 如果翻译失败，尝试直接读取翻译文件
            if ($message === $translationKey || strpos($message, 'blog.') === 0) {
                $translations = trans('blog', [], $locale);
                if (isset($translations['comment_login_required'])) {
                    $message = $translations['comment_login_required'];
                } else {
                    // 如果还是失败，使用备用消息
                    if ($lang === 'en') {
                        $message = 'Please login to post a comment';
                    } elseif ($lang === 'tw') {
                        $message = '請先登入後再發布評論';
                    } else {
                        $message = '请先登录后再发布评论';
                    }
                }
            }
            
            return response()->json([
                'code' => 401,
                'message' => $message,
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'content' => ['required', 'string', 'min:3', 'max:2000'],
            'parent_id' => ['nullable', 'integer', function ($attribute, $value, $fail) {
                if ($value && $value != 0) {
                    $exists = \App\Models\Blog\Comment::where('id', $value)->exists();
                    if (!$exists) {
                        $fail('父评论不存在');
                    }
                }
            }],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        // 获取原始评论内容
        $rawContent = trim($request->input('content'));

        // 内容安全过滤和敏感词检测
        $filterResult = CommentFilterService::filter($rawContent);

        // 如果检测到敏感词，拒绝提交
        if ($filterResult['has_sensitive_word']) {
            // 获取当前语言并设置语言环境
            // 优先从request attributes获取（由中间件设置），如果没有则直接从cookie读取
            $lang = $request->attributes->get('current_lang');
            if (!$lang) {
                // 直接从cookie读取（因为AJAX请求可能中间件没有设置attributes）
                $lang = $request->cookie('locale', 'cn');
            }
            
            // 语言代码映射：cookie中的语言代码 -> Laravel locale（与中间件保持一致）
            // 中间件使用：cn -> zh, en -> en, tw -> tw
            $langMap = ['cn' => 'zh', 'en' => 'en', 'tw' => 'tw'];
            $locale = $langMap[$lang] ?? 'zh';
            
            // 设置语言环境
            app()->setLocale($locale);
            
            // 构建敏感词列表字符串（根据语言使用不同的分隔符）
            $separator = ($lang === 'en') ? ', ' : '、';
            $sensitiveWordsList = implode($separator, $filterResult['sensitive_words']);
            
            // 使用翻译函数获取错误消息
            // 先尝试使用翻译函数
            $translationKey = 'blog.comment_sensitive_word_with_words';
            $message = trans($translationKey, ['words' => $sensitiveWordsList], $locale);
            
            // 如果翻译失败（返回翻译键本身），尝试直接读取翻译文件
            if ($message === $translationKey || strpos($message, 'blog.') === 0) {
                $translations = trans('blog', [], $locale);
                if (isset($translations['comment_sensitive_word_with_words'])) {
                    $message = str_replace(':words', $sensitiveWordsList, $translations['comment_sensitive_word_with_words']);
                } else {
                    // 如果还是失败，使用备用消息
                    if ($lang === 'en') {
                        $message = 'Comment contains sensitive words (' . $sensitiveWordsList . ') and cannot be submitted';
                    } elseif ($lang === 'tw') {
                        $message = '評論內容包含敏感詞（' . $sensitiveWordsList . '），無法提交';
                    } else {
                        $message = '评论内容包含敏感词（' . $sensitiveWordsList . '），无法提交';
                    }
                }
            }
            
            Log::warning('评论包含敏感词被拒绝', [
                'user_id' => auth()->id(),
                'post_id' => $post->id,
                'sensitive_words' => $filterResult['sensitive_words'],
                'content_preview' => mb_substr($rawContent, 0, 50, 'UTF-8'),
                'locale' => $locale,
                'lang' => $lang,
                'lang_from_attributes' => $request->attributes->get('current_lang'),
                'lang_from_cookie' => $request->cookie('locale'),
                'message' => $message,
            ]);

            return response()->json([
                'code' => 403,
                'message' => $message,
                'sensitive_words' => $filterResult['sensitive_words'], // 前端也可以使用
            ], 403);
        }

        // 创建评论
        try {
            // 检查是否需要审核评论
            $commentCheck = (int)ConfigHelper::get('comment_check', 0);
            $commentStatus = ($commentCheck === 1) ? 'pending' : 'approved';
            
            $comment = new Comment();
            $comment->post_id = $post->id;
            $comment->user_id = auth()->id(); // 必须已登录，所以一定有user_id
            $parentId = $request->input('parent_id');
            $comment->parent_id = ($parentId && $parentId > 0) ? $parentId : 0;
            $comment->content = $filterResult['content']; // 使用过滤后的内容
            $comment->status = $commentStatus; // 根据配置设置状态
            $comment->ip = $request->ip();
            $comment->ua = $request->userAgent() ?? '';
            
            if (!$comment->save()) {
                return response()->json([
                    'code' => 500,
                    'message' => '评论保存失败',
                ], 500);
            }
            
            // 根据审核状态返回不同的提示消息
            $lang = $request->attributes->get('current_lang');
            if (!$lang) {
                $lang = $request->cookie('locale', 'cn');
            }
            $langMap = ['cn' => 'zh', 'en' => 'en', 'tw' => 'tw'];
            $locale = $langMap[$lang] ?? 'zh';
            app()->setLocale($locale);
            
            if ($commentStatus === 'pending') {
                $successMessage = __('blog.comment_pending_review') ?? '评论已提交，等待审核';
            } else {
                $successMessage = __('blog.comment_success') ?? '评论提交成功！';
            }
        } catch (\Exception $e) {
            Log::error('评论保存失败: ' . $e->getMessage(), [
                'post_id' => $post->id,
                'content' => $rawContent,
                'parent_id' => $request->input('parent_id'),
                'error' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'code' => 500,
                'message' => '评论保存失败：' . $e->getMessage(),
            ], 500);
        }

        // 刷新文章评论数
        $post->refresh();

        return response()->json([
            'code' => 0,
            'message' => $successMessage,
            'data' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'status' => $comment->status,
                'created_at' => $comment->created_at->format('Y-m-d H:i'),
            ],
        ]);
    }
}

