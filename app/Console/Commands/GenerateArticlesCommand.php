<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Blog\ArticleGeneratorService;
use App\Models\LPadmin\Option;
use Illuminate\Support\Facades\Log;

class GenerateArticlesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:generate 
                            {--count= : 生成数量（可选，否则使用随机数量）}
                            {--keyword= : 指定关键词（可选，否则随机选择）}
                            {--category= : 指定分类ID（可选）}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动生成博客文章（定时任务或手动触发）';

    protected ArticleGeneratorService $generatorService;

    public function __construct(ArticleGeneratorService $generatorService)
    {
        parent::__construct();
        $this->generatorService = $generatorService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 增加执行时间限制
        set_time_limit(600); // 设置为 10 分钟（命令行模式可以更长）
        
        $this->info('开始生成博客文章...');

        // 检查是否开启自动生成
        $autoEnabled = Option::getValue('deepseek_auto_enabled', '0') === '1';
        
        // 如果是指定了参数，则视为手动触发，忽略自动生成开关
        $isManual = $this->option('count') !== null || $this->option('keyword') !== null;
        
        if (!$autoEnabled && !$isManual) {
            $this->warn('自动生成未开启，如需生成请使用 --count 参数手动触发');
            return 0;
        }

        // 获取生成数量
        $count = $this->option('count');
        if ($count === null) {
            // 随机生成数量
            $min = (int) Option::getValue('deepseek_daily_count_min', '1');
            $max = (int) Option::getValue('deepseek_daily_count_max', '3');
            $count = rand($min, $max);
        } else {
            $count = (int) $count;
        }

        if ($count <= 0) {
            $this->error('生成数量必须大于0');
            return 1;
        }

        $this->info("计划生成 {$count} 篇文章...");

        // 准备生成选项
        $options = [];
        
        if ($this->option('keyword')) {
            $options['keywords'] = [$this->option('keyword')];
        }
        
        if ($this->option('category')) {
            $options['category_id'] = (int) $this->option('category');
        }

        // 开始生成
        $startTime = microtime(true);
        $result = $this->generatorService->generateBatch($count, $options);
        $endTime = microtime(true);
        $duration = round($endTime - $startTime, 2);

        // 输出结果
        $this->info("生成完成！耗时: {$duration} 秒");
        $this->info("成功: {$result['success']} 篇");
        
        if ($result['failed'] > 0) {
            $this->error("失败: {$result['failed']} 篇");
        }

        // 显示生成的文章标题
        if (!empty($result['posts'])) {
            $this->info("\n生成的文章:");
            foreach ($result['posts'] as $post) {
                $title = is_array($post->title) 
                    ? ($post->title['zh'] ?? (array_values($post->title)[0] ?? '无标题'))
                    : ($post->title ?? '无标题');
                $this->line("  - [ID: {$post->id}] {$title}");
            }
        }

        // 记录日志
        Log::info('文章生成任务执行完成', [
            'count' => $count,
            'success' => $result['success'],
            'failed' => $result['failed'],
            'duration' => $duration,
        ]);

        return 0;
    }
}
