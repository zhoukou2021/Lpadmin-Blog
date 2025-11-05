<?php

namespace App\Services\Blog;

use App\Models\Blog\Post;
use App\Models\Blog\Category;
use App\Models\Blog\Tag;
use App\Models\LPadmin\Option;
use App\Services\DeepSeekService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class ArticleGeneratorService
{
    protected DeepSeekService $deepSeekService;

    public function __construct(DeepSeekService $deepSeekService)
    {
        $this->deepSeekService = $deepSeekService;
    }

    /**
     * 获取系统启用的语言列表（确保简体中文优先）
     */
    private function getEnabledLanguages(): array
    {
        try {
            $option = Option::where('name', 'lang')->first();
            if ($option && $option->value) {
                $codes = array_values(array_filter(array_map('trim', explode(',', (string)$option->value))));
                
                // 确保简体中文（cn）在第一位
                $sortedCodes = [];
                if (in_array('cn', $codes)) {
                    $sortedCodes[] = 'cn';
                }
                // 添加其他语言
                foreach ($codes as $code) {
                    if ($code !== 'cn' && !in_array($code, $sortedCodes)) {
                        $sortedCodes[] = $code;
                    }
                }
                
                return !empty($sortedCodes) ? $sortedCodes : ['cn'];
            }
        } catch (\Exception $e) {
            Log::warning('获取系统语言列表失败，使用默认值', ['error' => $e->getMessage()]);
        }
        
        return ['cn'];
    }

    /**
     * 获取生成规则
     */
    private function getPromptRules(): string
    {
        return Option::getValue('deepseek_prompt_rules', '');
    }

    /**
     * 选择分类（随机选择一个可见的分类，如果不存在则创建）
     */
    private function selectCategory(?int $categoryId = null): ?Category
    {
        if ($categoryId) {
            $category = Category::where('id', $categoryId)->where('visible', true)->first();
            if ($category) {
                return $category;
            }
        }

        // 随机选择
        $category = Category::where('visible', true)
            ->inRandomOrder()
            ->first();
            
        // 如果不存在分类，创建一个默认分类
        if (!$category) {
            $categoryNames = [];
            $languages = $this->getEnabledLanguages();
            foreach ($languages as $lang) {
                $categoryNames[$lang] = '默认分类';
            }
            
            // 先创建分类获取ID
            $category = Category::create([
                'name' => $categoryNames,
                'slug' => 'temp_' . time() . '_' . rand(1000, 9999), // 临时slug
                'visible' => true,
                'sort' => 100,
            ]);
            
            // 使用 category-{id} 格式更新 slug
            $category->slug = 'category-' . $category->id;
            $category->save();
        }
        
        return $category;
    }

    /**
     * 根据关键词选择或创建标签
     */
    private function selectOrCreateTags(string $keyword, ?array $tagIds = null): array
    {
        $tags = [];

        // 如果指定了标签ID，直接使用
        if ($tagIds && !empty($tagIds)) {
            $existingTags = Tag::whereIn('id', $tagIds)->get();
            foreach ($existingTags as $tag) {
                $tags[] = $tag->name;
            }
            return $tags;
        }

        // 根据关键词搜索已有标签
        $existingTag = Tag::where('name', 'like', "%{$keyword}%")
            ->first();

        if ($existingTag) {
            // 处理多语言标签名称
            if (is_array($existingTag->name)) {
                $tags[] = $existingTag->name['cn'] ?? (array_values($existingTag->name)[0] ?? $keyword);
            } else {
                $tags[] = $existingTag->name ?: $keyword;
            }
        } else {
            // 创建新标签
            $tagNames = [];
            $languages = $this->getEnabledLanguages();
            foreach ($languages as $lang) {
                $tagNames[$lang] = $keyword;
            }

            // 先创建标签获取ID
            $tag = Tag::create([
                'name' => $tagNames,
                'slug' => 'temp_' . time() . '_' . rand(1000, 9999), // 临时slug
            ]);
            
            // 使用 tag-{id} 格式更新 slug
            $tag->slug = 'tag-' . $tag->id;
            $tag->save();

            $tags[] = $keyword;
        }

        // 可以再随机选择1-2个相关标签
        $relatedTags = Tag::where('name', '!=', $keyword)
            ->inRandomOrder()
            ->limit(2)
            ->get();

        foreach ($relatedTags as $tag) {
            if (is_array($tag->name)) {
                $tagName = $tag->name['cn'] ?? (array_values($tag->name)[0] ?? '');
            } else {
                $tagName = $tag->name ?: '';
            }
            
            if (!empty($tagName) && !in_array($tagName, $tags)) {
                $tags[] = $tagName;
            }
        }

        return $tags;
    }

    /**
     * 生成单篇文章
     *
     * @param array $params 生成参数
     *   - keyword: 关键词（必需）
     *   - category_id: 分类ID（可选）
     *   - tag_ids: 标签ID数组（可选）
     * @param callable|null $logCallback 日志回调函数，接收 (type, message) 参数
     * @return Post|null
     */
    public function generateArticle(array $params, ?callable $logCallback = null): ?Post
    {
        try {
            // 确保数据库连接有效（防止长时间等待 API 响应导致连接超时）
            DB::reconnect();
            
            DB::beginTransaction();

            $keyword = $params['keyword'] ?? '';
            if (empty($keyword)) {
                throw new Exception('关键词不能为空');
            }

            if ($logCallback) {
                $logCallback('info', "使用关键词：{$keyword}");
            }

            // 选择分类
            $category = $this->selectCategory($params['category_id'] ?? null);
            if (!$category) {
                throw new Exception('没有可用的分类');
            }
            
            if ($logCallback) {
                $categoryName = is_array($category->name) 
                    ? ($category->name['cn'] ?? (array_values($category->name)[0] ?? ''))
                    : ($category->name ?? '');
                $logCallback('info', "选择分类：{$categoryName}");
            }

            // 获取分类名称（多语言）
            $categoryName = '';
            if (is_array($category->name)) {
                $categoryName = $category->name['cn'] ?? (array_values($category->name)[0] ?? '');
            } else {
                $categoryName = $category->name ?: '';
            }

            // 选择/创建标签
            $tags = $this->selectOrCreateTags($keyword, $params['tag_ids'] ?? null);
            if (empty($tags)) {
                $tags = [$keyword];
            }

            // 获取生成规则
            $rules = $this->getPromptRules();
            if (empty($rules)) {
                throw new Exception('生成规则未配置');
            }

            // 获取语言列表
            $languages = $this->getEnabledLanguages();
            
            if ($logCallback) {
                $logCallback('info', "生成语言：" . implode(', ', $languages));
                $logCallback('info', '开始调用 DeepSeek API 生成内容（这可能需要1-3分钟）...');
            }

            // 生成多语言内容（这可能需要很长时间，API 调用前先提交事务释放连接）
            // 临时提交事务，避免长时间占用数据库连接
            DB::commit();
            
            $multilingualContent = $this->deepSeekService->generateMultilingualContent(
                $keyword,
                $categoryName,
                $tags,
                $languages,
                $rules
            );
            
            if ($logCallback) {
                $logCallback('info', 'DeepSeek API 调用完成，开始保存文章...');
            }
            
            // API 调用完成后重新开始事务
            DB::reconnect();
            DB::beginTransaction();

            if (empty($multilingualContent)) {
                throw new Exception('生成内容为空');
            }

            // 组装多语言数据
            $title = [];
            $summary = [];
            $content = [];
            $metaTitle = [];
            $metaDesc = [];

            foreach ($languages as $lang) {
                if (isset($multilingualContent[$lang])) {
                    $data = $multilingualContent[$lang];
                    $title[$lang] = $data['title'] ?? '';
                    $summary[$lang] = $data['summary'] ?? '';
                    $content[$lang] = $data['content'] ?? '';
                    $metaTitle[$lang] = $data['meta_title'] ?? '';
                    $metaDesc[$lang] = $data['meta_description'] ?? '';
                }
            }

            // 如果没有生成任何内容，使用第一个语言的内容填充所有语言
            if (empty($title)) {
                $firstLang = $languages[0];
                $firstData = $multilingualContent[$firstLang] ?? null;
                if (!$firstData) {
                    throw new Exception('生成内容失败');
                }

                foreach ($languages as $lang) {
                    $title[$lang] = $firstData['title'] ?? '';
                    $summary[$lang] = $firstData['summary'] ?? '';
                    $content[$lang] = $firstData['content'] ?? '';
                    $metaTitle[$lang] = $firstData['meta_title'] ?? '';
                    $metaDesc[$lang] = $firstData['meta_description'] ?? '';
                }
            }

            // 生成 slug（先创建文章以获取ID，然后使用 article-{id} 格式）
            // 注意：这里先创建一个临时 slug，后续会更新为 article-{id}
            $tempSlug = 'temp_' . time() . '_' . rand(1000, 9999);
            
            // 获取简体中文标题（用于日志）
            $cnTitle = $title['cn'] ?? (array_values($title)[0] ?? '');

            // 获取是否自动发布
            $autoPublish = Option::getValue('deepseek_auto_publish', '0') === '1';
            $status = $autoPublish ? 'published' : 'draft';

            // 创建文章（先使用临时 slug）
            $post = Post::create([
                'author_id' => 1,
                'category_id' => $category->id,
                'status' => $status,
                'title' => $title,
                'slug' => $tempSlug,
                'summary' => $summary,
                'content' => $content,
                'meta_title' => $metaTitle,
                'meta_desc' => $metaDesc,
                'meta_json' => [
                    'keywords' => $multilingualContent['cn']['meta_keywords'] ?? (array_values($multilingualContent)[0]['meta_keywords'] ?? ''),
                ],
                'published_at' => $autoPublish ? now() : null,
                'recommend' => false,
            ]);
            
            // 使用 article-{id} 格式更新 slug，确保唯一性
            $slug = 'article-' . $post->id;
            $post->slug = $slug;
            $post->save();

            // 关联标签
            $tagModels = [];
            foreach ($tags as $tagName) {
                $tagModel = Tag::where(function($query) use ($tagName) {
                    $query->where('name', 'like', "%{$tagName}%")
                          ->orWhere('slug', Str::slug($tagName));
                })->first();

                if ($tagModel) {
                    $tagModels[] = $tagModel->id;
                }
            }

            if (!empty($tagModels)) {
                $post->tags()->sync($tagModels);
            }

            DB::commit();

            if ($logCallback) {
                $logCallback('success', "文章生成成功！ID: {$post->id}, Slug: {$slug}");
            }

            Log::info('文章生成成功', [
                'post_id' => $post->id,
                'title' => $cnTitle,
                'slug' => $slug,
                'keyword' => $keyword,
            ]);

            return $post;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('文章生成失败', [
                'keyword' => $params['keyword'] ?? '',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * 批量生成文章
     *
     * @param int $count 生成数量
     * @param array $options 选项
     *   - keywords: 关键词数组（可选，否则从配置读取）
     *   - category_id: 指定分类（可选）
     * @return array ['success' => 成功数量, 'failed' => 失败数量, 'posts' => 文章数组, 'logs' => 日志数组]
     */
    public function generateBatch(int $count, array $options = []): array
    {
        // 确保有足够的执行时间
        set_time_limit(300); // 5 分钟
        
        $success = 0;
        $failed = 0;
        $posts = [];
        $logs = []; // 收集生成过程的日志

        // 获取关键词列表
        $keywords = [];
        if (isset($options['keywords']) && is_array($options['keywords']) && !empty($options['keywords'])) {
            $keywords = $options['keywords'];
        } else {
            $keywordsStr = Option::getValue('deepseek_keywords', '');
            if (!empty($keywordsStr)) {
                $keywords = array_filter(array_map('trim', explode("\n", $keywordsStr)));
            }
        }

        if (empty($keywords)) {
            $errorMsg = '关键词列表为空，无法生成文章';
            Log::error($errorMsg);
            $logs[] = ['time' => now()->format('H:i:s'), 'type' => 'error', 'message' => $errorMsg];
            return ['success' => 0, 'failed' => 0, 'posts' => [], 'logs' => $logs, 'error' => $errorMsg];
        }

        $logs[] = ['time' => now()->format('H:i:s'), 'type' => 'info', 'message' => "开始生成 {$count} 篇文章..."];

        for ($i = 0; $i < $count; $i++) {
            $articleNum = $i + 1;
            $logs[] = ['time' => now()->format('H:i:s'), 'type' => 'info', 'message' => "--- 正在生成第 {$articleNum}/{$count} 篇文章 ---"];
            
            // 随机选择关键词
            $keyword = $keywords[array_rand($keywords)];
            $logs[] = ['time' => now()->format('H:i:s'), 'type' => 'info', 'message' => "关键词: {$keyword}"];

            $params = [
                'keyword' => $keyword,
            ];

            if (isset($options['category_id'])) {
                $params['category_id'] = $options['category_id'];
            }

            try {
                // 创建日志回调函数
                $logCallback = function($type, $message) use (&$logs) {
                    $logs[] = [
                        'time' => now()->format('H:i:s'),
                        'type' => $type,
                        'message' => $message
                    ];
                };
                
                $post = $this->generateArticle($params, $logCallback);

                if ($post) {
                    $success++;
                    $posts[] = $post;
                    $cnTitle = is_array($post->title) 
                        ? ($post->title['cn'] ?? (array_values($post->title)[0] ?? '无标题'))
                        : ($post->title ?? '无标题');
                    $logs[] = [
                        'time' => now()->format('H:i:s'), 
                        'type' => 'success', 
                        'message' => "✓ 第 {$articleNum} 篇文章生成成功：{$cnTitle} (ID: {$post->id}, Slug: {$post->slug})"
                    ];
                } else {
                    $failed++;
                    $logs[] = ['time' => now()->format('H:i:s'), 'type' => 'error', 'message' => "✗ 第 {$articleNum} 篇文章生成失败"];
                }
            } catch (\Exception $e) {
                $failed++;
                $errorMsg = "第 {$articleNum} 篇文章生成异常：" . $e->getMessage();
                $logs[] = ['time' => now()->format('H:i:s'), 'type' => 'error', 'message' => $errorMsg];
                Log::error($errorMsg, ['exception' => $e]);
            }

            // 每次生成后稍作延迟，避免 API 限流
            if ($i < $count - 1) {
                $logs[] = ['time' => now()->format('H:i:s'), 'type' => 'info', 'message' => "等待 2 秒后继续生成下一篇文章..."];
                sleep(2);
            }
        }

        $logs[] = ['time' => now()->format('H:i:s'), 'type' => 'info', 'message' => "=== 生成完成：成功 {$success} 篇，失败 {$failed} 篇 ==="];

        return [
            'success' => $success,
            'failed' => $failed,
            'posts' => $posts,
            'logs' => $logs,
        ];
    }

    /**
     * 批量生成文章（带进度回调）
     *
     * @param int $count 生成数量
     * @param array $options 选项
     * @param callable|null $progressCallback 进度回调函数，接收 (current, total, logEntry) 参数
     * @return array ['success' => 成功数量, 'failed' => 失败数量, 'posts' => 文章数组, 'logs' => 日志数组]
     */
    public function generateBatchWithProgress(int $count, array $options = [], ?callable $progressCallback = null): array
    {
        // 确保有足够的执行时间
        set_time_limit(600); // 10 分钟
        
        $success = 0;
        $failed = 0;
        $posts = [];
        $logs = []; // 收集生成过程的日志

        // 获取关键词列表
        $keywords = [];
        if (isset($options['keywords']) && is_array($options['keywords']) && !empty($options['keywords'])) {
            $keywords = $options['keywords'];
        } else {
            $keywordsStr = Option::getValue('deepseek_keywords', '');
            if (!empty($keywordsStr)) {
                $keywords = array_filter(array_map('trim', explode("\n", $keywordsStr)));
            }
        }

        if (empty($keywords)) {
            $errorMsg = '关键词列表为空，无法生成文章';
            Log::error($errorMsg);
            $logs[] = ['time' => now()->format('H:i:s'), 'type' => 'error', 'message' => $errorMsg];
            if ($progressCallback) {
                $progressCallback(0, $count, $logs[count($logs) - 1]);
            }
            return ['success' => 0, 'failed' => 0, 'posts' => [], 'logs' => $logs, 'error' => $errorMsg];
        }

        $logs[] = ['time' => now()->format('H:i:s'), 'type' => 'info', 'message' => "开始生成 {$count} 篇文章..."];
        if ($progressCallback) {
            try {
                $progressCallback(0, $count, $logs[count($logs) - 1]);
            } catch (\Exception $e) {
                Log::error('进度回调执行失败', ['error' => $e->getMessage()]);
            }
        }

        for ($i = 0; $i < $count; $i++) {
            $articleNum = $i + 1;
            $logs[] = ['time' => now()->format('H:i:s'), 'type' => 'info', 'message' => "--- 正在生成第 {$articleNum}/{$count} 篇文章 ---"];
            if ($progressCallback) {
                $progressCallback($i, $count, $logs[count($logs) - 1]);
            }
            
            // 随机选择关键词
            $keyword = $keywords[array_rand($keywords)];
            $logs[] = ['time' => now()->format('H:i:s'), 'type' => 'info', 'message' => "关键词: {$keyword}"];
            if ($progressCallback) {
                $progressCallback($i, $count, $logs[count($logs) - 1]);
            }

            $params = [
                'keyword' => $keyword,
            ];

            if (isset($options['category_id'])) {
                $params['category_id'] = $options['category_id'];
            }

            try {
                // 创建日志回调函数
                $logCallback = function($type, $message) use (&$logs, &$i, &$count, &$progressCallback) {
                    $logEntry = [
                        'time' => now()->format('H:i:s'),
                        'type' => $type,
                        'message' => $message
                    ];
                    $logs[] = $logEntry;
                    if ($progressCallback) {
                        $progressCallback($i + 1, $count, $logEntry);
                    }
                };
                
                $post = $this->generateArticle($params, $logCallback);

                if ($post) {
                    $success++;
                    $posts[] = $post;
                    $cnTitle = is_array($post->title) 
                        ? ($post->title['cn'] ?? (array_values($post->title)[0] ?? '无标题'))
                        : ($post->title ?? '无标题');
                    $logs[] = [
                        'time' => now()->format('H:i:s'), 
                        'type' => 'success', 
                        'message' => "✓ 第 {$articleNum} 篇文章生成成功：{$cnTitle} (ID: {$post->id}, Slug: {$post->slug})"
                    ];
                    if ($progressCallback) {
                        $progressCallback($i + 1, $count, $logs[count($logs) - 1]);
                    }
                } else {
                    $failed++;
                    $logs[] = ['time' => now()->format('H:i:s'), 'type' => 'error', 'message' => "✗ 第 {$articleNum} 篇文章生成失败"];
                    if ($progressCallback) {
                        $progressCallback($i + 1, $count, $logs[count($logs) - 1]);
                    }
                }
            } catch (\Exception $e) {
                $failed++;
                $errorMsg = "第 {$articleNum} 篇文章生成异常：" . $e->getMessage();
                $logs[] = ['time' => now()->format('H:i:s'), 'type' => 'error', 'message' => $errorMsg];
                if ($progressCallback) {
                    $progressCallback($i + 1, $count, $logs[count($logs) - 1]);
                }
                Log::error($errorMsg, ['exception' => $e]);
            }

            // 每次生成后稍作延迟，避免 API 限流
            if ($i < $count - 1) {
                $logs[] = ['time' => now()->format('H:i:s'), 'type' => 'info', 'message' => "等待 2 秒后继续生成下一篇文章..."];
                if ($progressCallback) {
                    $progressCallback($i + 1, $count, $logs[count($logs) - 1]);
                }
                sleep(2);
            }
        }

        $logs[] = ['time' => now()->format('H:i:s'), 'type' => 'info', 'message' => "=== 生成完成：成功 {$success} 篇，失败 {$failed} 篇 ==="];
        if ($progressCallback) {
            $progressCallback($count, $count, $logs[count($logs) - 1]);
        }

        return [
            'success' => $success,
            'failed' => $failed,
            'posts' => $posts,
            'logs' => $logs,
        ];
    }
}

