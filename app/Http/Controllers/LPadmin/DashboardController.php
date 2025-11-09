<?php

namespace App\Http\Controllers\LPadmin;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\LPadmin\Admin;
use App\Models\LPadmin\User;
use App\Models\LPadmin\Role;
use App\Models\LPadmin\Rule;
use App\Models\Blog\Post;
use App\Models\Blog\Like;
use App\Models\Blog\Comment;
use App\Models\Blog\Favorite;

/**
 * 仪表盘控制器
 *
 * 处理后台首页和统计数据
 */
class DashboardController extends BaseController
{
    /**
     * 显示仪表盘首页
     *
     * @return View
     */
    public function index(): View
    {
        // 获取统计数据
        $statistics = $this->getDashboardStatistics();

        return view('lpadmin.dashboard.index', compact('statistics'));
    }

    /**
     * 获取统计数据
     *
     * @return JsonResponse
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'admin_count' => Admin::count(),
                'user_count' => User::count(),
                'role_count' => Role::count(),
                'rule_count' => Rule::count(),
                'today_login_count' => Admin::whereDate('last_login_at', today())->count(),
                'online_count' => Admin::where('last_login_at', '>=', now()->subMinutes(30))->count(),
            ];

            return $this->success($stats);
        } catch (\Exception $e) {
            return $this->error('获取统计数据失败: ' . $e->getMessage());
        }
    }

    /**
     * 获取系统信息
     *
     * @return JsonResponse
     */
    public function systemInfo(): JsonResponse
    {
        try {
            $info = [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'database_version' => DB::select('SELECT VERSION() as version')[0]->version ?? 'Unknown',
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'disk_free_space' => $this->formatBytes(disk_free_space('/')),
                'disk_total_space' => $this->formatBytes(disk_total_space('/')),
            ];

            return $this->success($info);
        } catch (\Exception $e) {
            return $this->error('获取系统信息失败: ' . $e->getMessage());
        }
    }

    /**
     * 获取最近登录记录
     *
     * @return JsonResponse
     */
    public function recentLogins(): JsonResponse
    {
        try {
            $logins = Admin::select(['id', 'username', 'nickname', 'last_login_at', 'last_login_ip'])
                ->whereNotNull('last_login_at')
                ->orderBy('last_login_at', 'desc')
                ->limit(10)
                ->get();

            return $this->success($logins);
        } catch (\Exception $e) {
            return $this->error('获取登录记录失败: ' . $e->getMessage());
        }
    }

    /**
     * 格式化字节数
     *
     * @param int $bytes
     * @return string
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * 获取仪表盘统计数据
     */
    private function getDashboardStatistics(): array
    {
        // 博客统计
        $blogStats = [
            'user_count' => User::count(),
            'post_count' => Post::count(),
            'like_count' => Like::count(),
            'comment_count' => Comment::count(),
            'favorite_count' => Favorite::count(),
            'total_views' => Post::where('status', 'published')->sum('view_count') ?? 0,
        ];

        return [
            'blog' => $blogStats,
        ];
    }

    /**
     * 获取访问量前十的页面
     *
     * @return JsonResponse
     */
    public function topPages(): JsonResponse
    {
        try {
            $lang = request()->input('lang', 'cn');
            
            $topPages = Post::where('status', 'published')
                ->select('id', 'slug', 'title', 'view_count', 'published_at')
                ->orderByDesc('view_count')
                ->limit(10)
                ->get()
                ->map(function($post) use ($lang) {
                    return [
                        'id' => $post->id,
                        'slug' => $post->slug ?? ('article_' . $post->id),
                        'title' => get_i18n_value($post->title, $lang, ''),
                        'view_count' => $post->view_count ?? 0,
                        'published_at' => $post->published_at ? $post->published_at->format('Y-m-d') : '',
                    ];
                });

            return $this->success($topPages);
        } catch (\Exception $e) {
            return $this->error('获取访问量前十页面失败: ' . $e->getMessage());
        }
    }

    /**
     * 获取趋势数据
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function trendData(Request $request): JsonResponse
    {
        try {
            $days = (int)($request->input('days', 7)); // 默认7天
            $days = min(max($days, 7), 30); // 限制在7-30天之间

            // 生成日期数组
            $dates = [];
            $userData = [];
            $likeData = [];
            $commentData = [];
            $favoriteData = [];

            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $startOfDay = now()->subDays($i)->startOfDay()->copy();
                $endOfDay = now()->subDays($i)->endOfDay()->copy();

                $dates[] = $date;
                $userData[] = User::whereBetween('created_at', [$startOfDay, $endOfDay])->count();
                $likeData[] = Like::whereBetween('created_at', [$startOfDay, $endOfDay])->count();
                $commentData[] = Comment::whereBetween('created_at', [$startOfDay, $endOfDay])->count();
                $favoriteData[] = Favorite::whereBetween('created_at', [$startOfDay, $endOfDay])->count();
            }

            return $this->success([
                'dates' => $dates,
                'user' => $userData,
                'like' => $likeData,
                'comment' => $commentData,
                'favorite' => $favoriteData,
            ]);
        } catch (\Exception $e) {
            return $this->error('获取趋势数据失败: ' . $e->getMessage());
        }
    }
}
