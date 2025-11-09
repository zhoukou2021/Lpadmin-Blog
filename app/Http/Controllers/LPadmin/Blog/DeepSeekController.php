<?php

namespace App\Http\Controllers\LPadmin\Blog;

use App\Http\Controllers\LPadmin\BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\LPadmin\Option;
use App\Services\Blog\ArticleGeneratorService;
use App\Models\Blog\Category;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DeepSeekController extends BaseController
{
    protected ArticleGeneratorService $generatorService;

    public function __construct(ArticleGeneratorService $generatorService)
    {
        $this->generatorService = $generatorService;
    }

    /**
     * 配置管理页面
     */
    public function index(): View
    {
        // 获取 deepseek 分组的所有配置
        $options = Option::where('group', 'deepseek')
            ->orderBy('sort')
            ->orderBy('id')
            ->get();

        return view('lpadmin.blog.deepseek.index', compact('options'));
    }

    /**
     * 保存配置
     */
    public function store(Request $request): JsonResponse
    {
        // 处理布尔值字段，确保它们存在并转换为标准格式
        $input = $request->all();
        
        // 记录原始数据用于调试
        Log::debug('DeepSeek 配置保存 - 原始数据', ['input' => $input]);
        
        if (!isset($input['deepseek_auto_enabled'])) {
            $input['deepseek_auto_enabled'] = '0';
        }
        if (!isset($input['deepseek_auto_publish'])) {
            $input['deepseek_auto_publish'] = '0';
        }
        
        // 将布尔值转换为字符串 "1" 或 "0"，Laravel boolean 验证支持这种格式
        $input['deepseek_auto_enabled'] = in_array($input['deepseek_auto_enabled'], ['1', 1, true, 'true', 'on', 'yes'], true) ? '1' : '0';
        $input['deepseek_auto_publish'] = in_array($input['deepseek_auto_publish'], ['1', 1, true, 'true', 'on', 'yes'], true) ? '1' : '0';
        
        // 记录转换后的数据
        Log::debug('DeepSeek 配置保存 - 转换后数据', ['input' => $input]);
        
        $validator = Validator::make($input, [
            'deepseek_api_key' => 'nullable|string',
            'deepseek_auto_enabled' => 'required|boolean',
            'deepseek_auto_publish' => 'required|boolean',
            'deepseek_daily_count_min' => 'nullable|integer|min:1',
            'deepseek_daily_count_max' => 'nullable|integer|min:1|gte:deepseek_daily_count_min',
            'deepseek_keywords' => 'nullable|string',
            'deepseek_prompt_rules' => 'nullable|string',
            'deepseek_model' => 'nullable|string',
        ], [
            'deepseek_daily_count_max.gte' => '最多生成条数不能少于最少生成条数',
        ]);

        if ($validator->fails()) {
            // 记录验证错误详情
            Log::debug('DeepSeek 配置保存 - 验证失败', [
                'errors' => $validator->errors()->toArray(),
                'input' => $input
            ]);
            return $this->error($validator->errors()->first());
        }

        try {
            // 使用验证后的数据
            $validated = $validator->validated();

            foreach ($validated as $name => $value) {
                if ($value !== null) {
                    // 处理布尔值
                    if (in_array($name, ['deepseek_auto_enabled', 'deepseek_auto_publish'])) {
                        $value = $value ? '1' : '0';
                    }
                    Option::setValue($name, $value);
                }
            }

            // 清除配置缓存
            Option::clearCache();

            return $this->success([], '配置保存成功');
        } catch (\Exception $e) {
            Log::error('保存 DeepSeek 配置失败', ['error' => $e->getMessage()]);
            return $this->error('配置保存失败：' . $e->getMessage());
        }
    }

    /**
     * 手动生成文章（简化版本，直接返回成功）
     */
    public function generate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'count' => 'nullable|integer|min:1|max:10',
            'keyword' => 'nullable|string|max:100',
            'category_id' => 'nullable|integer|exists:blog_categories,id',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        // 检查 API Key
        $apiKey = Option::getValue('deepseek_api_key');
        if (empty($apiKey)) {
            return $this->error('请先配置 DeepSeek API Key');
        }

        $count = $request->input('count', 1);
        $options = [];

        if ($request->filled('keyword')) {
            $options['keywords'] = [$request->input('keyword')];
        }

        if ($request->filled('category_id')) {
            $options['category_id'] = (int) $request->input('category_id');
        }

        // 记录生成请求
        Log::info('创建文章生成任务', [
            'count' => $count,
            'options' => $options,
        ]);

        // 先返回成功响应给客户端
        $response = $this->success(null, '文章生成队列已添加，请稍后在博客列表页查看。');

        // 如果是 FastCGI，立即结束请求
        if (function_exists('fastcgi_finish_request')) {
            ignore_user_abort(true);
            fastcgi_finish_request();
        }

        // 在后台异步执行生成任务（不等待结果）
        try {
            $this->generateInBackground($count, $options);
        } catch (\Exception $e) {
            Log::error('后台生成任务启动失败', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return $response;
    }

    /**
     * 后台生成文章（简化版本，不跟踪进度）
     */
    private function generateInBackground(int $count, array $options): void
    {
        // 增加执行时间限制
        set_time_limit(600); // 10 分钟

        try {
            Log::info('开始后台生成文章', [
                'count' => $count,
                'options' => $options,
            ]);

            // 直接调用生成服务，不使用进度回调
            $result = $this->generatorService->generateBatch($count, $options);

            Log::info('文章生成完成', [
                'success' => $result['success'] ?? 0,
                'failed' => $result['failed'] ?? 0,
            ]);
        } catch (\Exception $e) {
            Log::error('后台生成文章失败', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }


    /**
     * 获取配置数据（AJAX）
     */
    public function getConfig(): JsonResponse
    {
        $configs = Option::where('group', 'deepseek')
            ->orderBy('sort')
            ->orderBy('id')
            ->get()
            ->mapWithKeys(function($option) {
                $value = $option->value;
                
                // 处理布尔值
                if (in_array($option->type, [Option::TYPE_SWITCH, Option::TYPE_CHECKBOX])) {
                    $value = (bool) $value;
                }
                
                // 处理数字
                if ($option->type === Option::TYPE_NUMBER) {
                    $value = (int) $value;
                }
                
                return [$option->name => $value];
            });

        return $this->success($configs->toArray());
    }
}
