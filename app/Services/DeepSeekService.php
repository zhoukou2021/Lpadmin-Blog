<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\LPadmin\Option;

class DeepSeekService
{
    /**
     * API 基础 URL
     */
    private const API_BASE_URL = 'https://api.deepseek.com/v1/chat/completions';

    /**
     * 最大重试次数
     */
    private const MAX_RETRIES = 3;

    /**
     * 重试延迟（秒）
     */
    private const RETRY_DELAY = 2;

    /**
     * 获取 API Key
     */
    private function getApiKey(): ?string
    {
        return Option::getValue('deepseek_api_key');
    }

    /**
     * 获取模型名称
     */
    private function getModel(): string
    {
        return Option::getValue('deepseek_model', 'deepseek-chat');
    }

    /**
     * 发送生成请求到 DeepSeek API
     *
     * @param array $messages 消息数组
     * @param string|null $model 模型名称，如果为null则使用配置中的模型
     * @return array 返回解析后的内容
     * @throws \Exception
     */
    public function generateContent(array $messages, ?string $model = null): array
    {
        $apiKey = $this->getApiKey();
        if (empty($apiKey)) {
            throw new \Exception('DeepSeek API Key 未配置');
        }

        $model = $model ?? $this->getModel();
        $retries = 0;

        while ($retries < self::MAX_RETRIES) {
            try {
                // 创建 HTTP 客户端（增加超时时间，因为 AI 生成可能需要较长时间）
                $client = Http::timeout(180); // 增加到 3 分钟
                
                // 在开发环境或配置允许时禁用 SSL 验证（解决 Windows 开发环境 SSL 证书问题）
                if (config('app.env') === 'local' || config('app.debug')) {
                    $client = $client->withoutVerifying();
                }
                
                $client = $client->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ]);

                $response = $client->post(self::API_BASE_URL, [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => 6000, // 增加 token 限制，确保内容完整（从 4000 增加到 6000）
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    // 检查响应结构
                    if (!isset($data['choices']) || !is_array($data['choices']) || empty($data['choices'])) {
                        Log::error('DeepSeek API 响应结构异常', ['response' => $data]);
                        throw new \Exception('API 返回结构异常：缺少 choices 字段');
                    }
                    
                    $content = $data['choices'][0]['message']['content'] ?? '';
                    
                    if (empty($content)) {
                        Log::error('DeepSeek API 返回内容为空', ['data' => $data]);
                        throw new \Exception('API 返回内容为空');
                    }
                    
                    // 检查内容是否可能被截断（如果 finish_reason 不是 'stop'）
                    $finishReason = $data['choices'][0]['finish_reason'] ?? null;
                    $isTruncated = false;
                    
                    if ($finishReason !== 'stop' && $finishReason !== null) {
                        Log::warning('DeepSeek API 响应可能未完成', [
                            'finish_reason' => $finishReason,
                            'content_length' => strlen($content),
                            'content_preview' => mb_substr($content, 0, 200),
                        ]);
                        $isTruncated = true;
                        // 如果是因为长度限制被截断，可能需要增加 max_tokens
                        if ($finishReason === 'length') {
                            Log::warning('DeepSeek API 响应因 token 限制被截断，建议增加 max_tokens');
                        }
                    }
                    
                    // 检查内容是否以 ```json 开头（说明可能包含 markdown 代码块）
                    if (strpos($content, '```json') !== false || strpos($content, '```') !== false) {
                        Log::info('DeepSeek API 返回内容包含 markdown 代码块标记', [
                            'content_preview' => mb_substr($content, 0, 200),
                            'has_json_block' => strpos($content, '```json') !== false,
                        ]);
                    }

                    return $this->parseResponse($content, $isTruncated);
                } else {
                    $error = $response->json();
                    $errorMsg = $error['error']['message'] ?? 'API 请求失败';
                    $statusCode = $response->status();

                    // 如果是速率限制，等待后重试
                    if ($statusCode === 429 && $retries < self::MAX_RETRIES - 1) {
                        $waitTime = self::RETRY_DELAY * ($retries + 1);
                        Log::warning("DeepSeek API 速率限制，等待 {$waitTime} 秒后重试");
                        sleep($waitTime);
                        $retries++;
                        continue;
                    }

                    throw new \Exception("API 请求失败: {$errorMsg} (HTTP {$statusCode})");
                }
            } catch (\Exception $e) {
                $retries++;
                if ($retries >= self::MAX_RETRIES) {
                    Log::error('DeepSeek API 调用失败', [
                        'error' => $e->getMessage(),
                        'retries' => $retries,
                    ]);
                    throw $e;
                }

                // 等待后重试
                sleep(self::RETRY_DELAY * $retries);
            }
        }

        throw new \Exception('DeepSeek API 调用失败，已达到最大重试次数');
    }

    /**
     * 构建提示词
     *
     * @param string $keyword 关键词
     * @param string $categoryName 分类名称
     * @param array $tags 标签数组
     * @param string $rules 生成规则
     * @param string $language 语言代码（zh, en, tw等）
     * @return string 构建后的提示词
     */
    public function buildPrompt(string $keyword, string $categoryName, array $tags, string $rules, string $language = 'zh'): string
    {
        $tagsStr = implode(', ', $tags);
        
        // 根据语言设置提示词语言
        $languageMap = [
            'cn' => '简体中文',
            'zh' => '简体中文', // 兼容旧代码
            'en' => 'English',
            'tw' => '繁體中文',
        ];
        $langLabel = $languageMap[$language] ?? '简体中文';
        
        $prompt = $rules;
        $prompt = str_replace('{category_name}', $categoryName, $prompt);
        $prompt = str_replace('{keyword}', $keyword, $prompt);
        $prompt = str_replace('{tags}', $tagsStr, $prompt);
        
        // 添加语言要求
        if ($language !== 'cn' && $language !== 'zh') {
            $prompt = "请使用 {$langLabel} 语言生成文章内容。\n\n" . $prompt;
        }
        
        return $prompt;
    }

    /**
     * 解析 API 响应内容
     *
     * @param string $response API 返回的原始内容
     * @param bool $isTruncated 内容是否可能被截断
     * @return array 解析后的文章数据
     * @throws \Exception
     */
    public function parseResponse(string $response, bool $isTruncated = false): array
    {
        // 清理响应内容，移除可能的 markdown 代码块标记
        $content = trim($response);
        
        // 如果包含 ```json 或 ``` 标记，提取其中的 JSON（优先处理）
        // 尝试多种匹配模式来处理不同的情况
        
        // 模式1：完整的 ```json ... ``` 格式
        if (preg_match('/```(?:json)?\s*\n?(.*?)\n?\s*```/s', $content, $matches)) {
            $extracted = trim($matches[1]);
            Log::debug('从 markdown 代码块中提取 JSON (完整格式)', ['length' => strlen($extracted)]);
            $content = $extracted;
        }
        // 模式2：只有开始的 ```json，没有结束的 ```
        else if (preg_match('/```(?:json)?\s*\n?(.*)$/s', $content, $matches)) {
            $extracted = trim($matches[1]);
            // 移除可能残留的 ``` 标记（如果存在）
            $extracted = preg_replace('/\s*```\s*$/', '', $extracted);
            Log::debug('从 markdown 代码块中提取 JSON (未闭合格式)', ['length' => strlen($extracted)]);
            $content = $extracted;
        }
        
        // 如果内容仍然包含 ```，移除所有 markdown 标记（兜底处理）
        $content = preg_replace('/^```(?:json)?\s*/', '', $content);
        $content = preg_replace('/\s*```\s*$/', '', $content);
        $content = trim($content);
        
        // 检查内容是否可能被截断（如果最后一个 } 不存在或不完整）
        $openBraces = substr_count($content, '{');
        $closeBraces = substr_count($content, '}');
        if ($openBraces > $closeBraces) {
            Log::warning('DeepSeek API 返回的 JSON 可能不完整，尝试补全', [
                'open_braces' => $openBraces,
                'close_braces' => $closeBraces,
                'content_length' => strlen($content),
                'last_100_chars' => mb_substr($content, -100),
                'is_truncated' => $isTruncated,
            ]);
            // 尝试补全 JSON（添加缺失的闭合括号）
            $missingBraces = $openBraces - $closeBraces;
            $content .= str_repeat('}', $missingBraces);
        }
        
        // 先清理控制字符
        $content = $this->cleanJsonString($content);
        
        // 尝试解析 JSON，使用多个选项来处理各种情况
        $jsonOptions = JSON_INVALID_UTF8_IGNORE | JSON_INVALID_UTF8_SUBSTITUTE;
        
        // 如果 PHP 版本支持，添加更多选项
        if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
            $jsonOptions |= JSON_INVALID_UTF8_SUBSTITUTE;
        }
        
        $data = json_decode($content, true, 512, $jsonOptions);
        
        // 如果 json_decode 返回 null 但 json_last_error 为 0，可能是字符串不是有效的 JSON
        if ($data === null && json_last_error() === JSON_ERROR_NONE) {
            // 尝试清理后重新解析
            $cleanedContent = $this->cleanJsonString($content);
            $data = json_decode($cleanedContent, true, 512, $jsonOptions);
        }
        
        if (json_last_error() !== JSON_ERROR_NONE || $data === null || !is_array($data)) {
            // 如果解析失败，尝试更激进的清理方法
            $cleanedContent = $this->cleanJsonStringAggressive($content);
            $data = json_decode($cleanedContent, true, 512, $jsonOptions);
            
            if (json_last_error() !== JSON_ERROR_NONE || $data === null || !is_array($data)) {
                // 尝试使用智能补全方法
                $fixedJson = $this->fixTruncatedJson($cleanedContent, $isTruncated);
                if ($fixedJson !== $cleanedContent) {
                    Log::debug('尝试修复被截断的 JSON', [
                        'original_length' => strlen($cleanedContent),
                        'fixed_length' => strlen($fixedJson),
                    ]);
                    $data = json_decode($fixedJson, true, 512, $jsonOptions);
                }
                
                // 如果还是失败，最后尝试提取 JSON 部分并补全
                if (json_last_error() !== JSON_ERROR_NONE || $data === null || !is_array($data)) {
                    // 使用更宽松的正则表达式匹配 JSON 开始部分
                    if (preg_match('/\{.*$/s', $cleanedContent, $jsonMatches)) {
                        $partialJson = $jsonMatches[0];
                        Log::debug('提取部分 JSON 并尝试补全', [
                            'partial_length' => strlen($partialJson),
                            'last_50_chars' => mb_substr($partialJson, -50),
                        ]);
                        
                        // 使用智能补全方法
                        $partialJson = $this->fixTruncatedJson($partialJson, true);
                        $finalJson = $this->cleanJsonStringAggressive($partialJson);
                        $data = json_decode($finalJson, true, 512, $jsonOptions);
                    }
                }
            }
            
            if (json_last_error() !== JSON_ERROR_NONE || $data === null || !is_array($data)) {
                Log::error('DeepSeek API 返回内容解析失败', [
                    'response_length' => strlen($content),
                    'response_preview' => mb_substr($content, 0, 500) . '...' . mb_substr($content, -200),
                    'open_braces' => substr_count($content, '{'),
                    'close_braces' => substr_count($content, '}'),
                    'error' => json_last_error() === JSON_ERROR_NONE ? 'Invalid JSON format (null returned)' : json_last_error_msg(),
                    'error_code' => json_last_error(),
                ]);
                throw new \Exception('API 返回内容格式错误，无法解析 JSON: ' . (json_last_error() === JSON_ERROR_NONE ? 'Invalid JSON format' : json_last_error_msg()) . ' (错误代码: ' . json_last_error() . ')');
            }
        }

        // 验证必需字段
        $requiredFields = ['title', 'summary', 'content', 'meta_title', 'meta_keywords', 'meta_description'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                Log::warning("DeepSeek API 返回内容缺少字段: {$field}", ['data' => $data]);
                throw new \Exception("API 返回内容缺少必需字段: {$field}");
            }
        }

        // 转换内容格式为HTML（如果内容是Markdown或纯文本）
        $htmlContent = $this->convertToHtml((string) $data['content']);
        
        return [
            'title' => (string) $data['title'],
            'summary' => (string) $data['summary'],
            'content' => $htmlContent,
            'meta_title' => (string) $data['meta_title'],
            'meta_keywords' => (string) $data['meta_keywords'],
            'meta_description' => (string) $data['meta_description'],
        ];
    }

    /**
     * 将内容转换为HTML格式（TinyMCE兼容）
     *
     * @param string $content 原始内容（可能是HTML、Markdown或纯文本）
     * @return string HTML格式的内容
     */
    private function convertToHtml(string $content): string
    {
        $content = trim($content);
        
        // 处理转义的换行符 \n（字面量字符串）
        // 将字面量 \n 转换为实际换行符
        $content = str_replace('\\n', "\n", $content);
        // 将字面量 \r\n 转换为实际换行符
        $content = str_replace('\\r\\n', "\n", $content);
        // 将字面量 \r 转换为实际换行符
        $content = str_replace('\\r', "\n", $content);
        // 将字面量 \t 转换为实际制表符
        $content = str_replace('\\t', "\t", $content);
        
        // 如果内容已经是HTML格式（包含HTML标签），需要清理和规范化
        if (preg_match('/<[a-z][\s\S]*>/i', $content)) {
            $content = $this->cleanHtmlContent($content);
            return $content;
        }
        
        // 如果是Markdown格式（包含 ## 或 ** 等Markdown语法），转换为HTML
        if (preg_match('/#{1,6}\s+|(\*\*|__|~~)/', $content)) {
            $content = $this->markdownToHtml($content);
        } else {
            // 纯文本，转换为HTML段落
            $content = $this->textToHtml($content);
        }
        
        // 最终清理：移除多余的空白和换行
        $content = $this->cleanHtmlContent($content);
        
        return $content;
    }
    
    /**
     * 清理HTML内容，移除多余的换行符和空白
     *
     * @param string $html HTML内容
     * @return string 清理后的HTML内容
     */
    private function cleanHtmlContent(string $html): string
    {
        // 先保护代码块，避免处理其内部内容
        $codeBlocks = [];
        $placeholderIndex = 0;
        
        // 提取所有 <pre><code>...</code></pre> 代码块
        $html = preg_replace_callback(
            '/<pre><code>([\s\S]*?)<\/code><\/pre>/i',
            function($matches) use (&$codeBlocks, &$placeholderIndex) {
                $placeholder = "___CODE_BLOCK_" . $placeholderIndex++ . "___";
                $codeContent = $matches[1];
                
                // 检查是否已经经过 HTML 转义（包含 &lt; 等）
                $needsEscape = !preg_match('/&(lt|gt|amp|quot|#\d+);/i', $codeContent);
                
                if ($needsEscape) {
                    // 对代码内容进行 HTML 转义
                    $codeContent = htmlspecialchars($codeContent, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
                
                // 移除转义的换行符字符序列（字面量 \n）
                $codeContent = str_replace(['\\n', '\\r', '\\r\\n'], '', $codeContent);
                
                // 清理代码块内多余的空行（保留换行结构）
                $codeContent = preg_replace('/\n{3,}/', "\n\n", $codeContent);
                // 移除首尾空白和换行
                $codeContent = trim($codeContent);
                
                $codeBlocks[$placeholder] = '<pre><code>' . $codeContent . '</code></pre>';
                return $placeholder;
            },
            $html
        );
        
        // 提取行内代码 <code>...</code>（在代码块提取后，剩余内容中的 <code> 都是行内代码）
        // 注意：因为代码块已经被占位符替换，所以这里的 <code> 都是行内代码
        $html = preg_replace_callback(
            '/<code>([^<]*(?:<(?!\/code>)[^<]*)*)<\/code>/i',
            function($matches) use (&$codeBlocks, &$placeholderIndex) {
                $placeholder = "___INLINE_CODE_" . $placeholderIndex++ . "___";
                $codeContent = trim($matches[1]);
                
                // 检查是否已经经过 HTML 转义
                $needsEscape = !preg_match('/&(lt|gt|amp|quot|#\d+);/i', $codeContent);
                
                if ($needsEscape) {
                    $codeContent = htmlspecialchars($codeContent, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
                
                // 移除转义的换行符字符序列（字面量 \n）
                $codeContent = str_replace(['\\n', '\\r', '\\r\\n'], '', $codeContent);
                
                // 移除换行符（行内代码不应该有换行）
                $codeContent = str_replace(["\n", "\r"], ' ', $codeContent);
                $codeContent = preg_replace('/\s+/', ' ', $codeContent);
                $codeBlocks[$placeholder] = '<code>' . $codeContent . '</code>';
                return $placeholder;
            },
            $html
        );
        
        // 移除 HTML 标签外的多余换行符（保留换行结构用于段落分割）
        // 将连续的换行符（3个以上）压缩为两个换行符
        $html = preg_replace('/\n{3,}/', "\n\n", $html);
        
        // 移除 HTML 标签之间的空白和换行（但保留标签结构）
        // 将标签之间的单独换行符移除
        $html = preg_replace('/>\s*\n\s*</', '><', $html);
        // 将标签之间的多个空白压缩为单个空格
        $html = preg_replace('/>\s{2,}</', '> <', $html);
        
        // 移除段落标签内的首尾空白
        $html = preg_replace('/<p>\s+/', '<p>', $html);
        $html = preg_replace('/\s+<\/p>/', '</p>', $html);
        
        // 恢复代码块
        foreach ($codeBlocks as $placeholder => $codeBlock) {
            $html = str_replace($placeholder, $codeBlock, $html);
        }
        
        // 最终清理：移除 HTML 内容中剩余的转义换行符字符序列（如果有遗漏）
        // 这些不应该出现在最终的 HTML 中
        $html = preg_replace('/&lt;br\s*\/?&gt;\s*/i', '<br>', $html); // 恢复转义的 br 标签
        
        // 移除字面量的 \n、\r、\t 字符序列（转义的字符串）
        $html = str_replace(['\\n', '\\r', '\\t'], '', $html);
        
        return trim($html);
    }

    /**
     * 将Markdown转换为HTML
     *
     * @param string $markdown Markdown文本
     * @return string HTML文本
     */
    private function markdownToHtml(string $markdown): string
    {
        $html = $markdown;
        
        // 转换标题
        $html = preg_replace('/^### (.*)$/m', '<h3>$1</h3>', $html);
        $html = preg_replace('/^## (.*)$/m', '<h2>$1</h2>', $html);
        $html = preg_replace('/^# (.*)$/m', '<h1>$1</h1>', $html);
        
        // 转换加粗和斜体
        $html = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $html);
        $html = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $html);
        $html = preg_replace('/__(.*?)__/', '<strong>$1</strong>', $html);
        $html = preg_replace('/_(.*?)_/', '<em>$1</em>', $html);
        
        // 转换代码块（先处理代码块，避免行内代码误匹配）
        // 匹配 ```语言\n内容``` 格式
        $html = preg_replace_callback(
            '/```(\w+)?\s*\n?(.*?)```/s',
            function($matches) {
                $codeContent = trim($matches[2]);
                // 对代码内容进行 HTML 转义
                $codeContent = htmlspecialchars($codeContent, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                // 移除首尾多余的空行
                $codeContent = preg_replace('/^\n+|\n+$/', '', $codeContent);
                return '<pre><code>' . $codeContent . '</code></pre>';
            },
            $html
        );
        
        // 转换行内代码（在代码块处理之后）
        $html = preg_replace_callback(
            '/`([^`\n]+?)`/',
            function($matches) {
                $codeContent = trim($matches[1]);
                // 对代码内容进行 HTML 转义
                $codeContent = htmlspecialchars($codeContent, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                // 移除换行符（行内代码不应该有换行）
                $codeContent = str_replace(["\n", "\r"], ' ', $codeContent);
                $codeContent = preg_replace('/\s+/', ' ', $codeContent);
                return '<code>' . $codeContent . '</code>';
            },
            $html
        );
        
        // 转换列表
        $lines = explode("\n", $html);
        $inList = false;
        $listItems = [];
        
        foreach ($lines as $line) {
            if (preg_match('/^[-*+]\s+(.*)$/', $line, $matches)) {
                if (!$inList) {
                    $inList = true;
                }
                $listItems[] = '<li>' . trim($matches[1]) . '</li>';
            } elseif (preg_match('/^\d+\.\s+(.*)$/', $line, $matches)) {
                if (!$inList) {
                    $inList = true;
                }
                $listItems[] = '<li>' . trim($matches[1]) . '</li>';
            } else {
                if ($inList && !empty($listItems)) {
                    $html = str_replace(implode("\n", array_slice($lines, array_search($line, $lines) - count($listItems), count($listItems))), 
                        '<ul>' . implode("\n", $listItems) . '</ul>', $html);
                    $inList = false;
                    $listItems = [];
                }
            }
        }
        
        // 转换段落（将连续的文本行包裹在<p>标签中）
        $html = preg_replace('/\n\n+/', "\n\n", $html);
        $paragraphs = explode("\n\n", $html);
        $html = '';
        foreach ($paragraphs as $para) {
            $para = trim($para);
            if (!empty($para)) {
                // 如果不是HTML标签开头，包裹在<p>中
                if (!preg_match('/^<[a-z]/i', $para)) {
                    $html .= '<p>' . $para . '</p>';
                } else {
                    $html .= $para;
                }
            }
        }
        
        return $html;
    }

    /**
     * 将纯文本转换为HTML段落
     *
     * @param string $text 纯文本
     * @return string HTML文本
     */
    private function textToHtml(string $text): string
    {
        // 按双换行符分割段落
        $paragraphs = preg_split('/\n\s*\n/', $text);
        $html = '';
        
        foreach ($paragraphs as $para) {
            $para = trim($para);
            if (!empty($para)) {
                // HTML 转义
                $para = htmlspecialchars($para, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                // 替换单换行符为<br>（保留段落内的换行）
                $para = preg_replace('/\n/', '<br>', $para);
                // 清理多余的 <br> 标签
                $para = preg_replace('/(<br>\s*){3,}/', '<br><br>', $para);
                $html .= '<p>' . $para . '</p>';
            }
        }
        
        return $html;
    }

    /**
     * 生成多语言内容（为每种语言调用 API）
     *
     * @param string $keyword 关键词
     * @param string $categoryName 分类名称
     * @param array $tags 标签数组
     * @param array $languages 语言代码数组，如 ['zh', 'en', 'tw']
     * @param string $rules 生成规则
     * @return array 多语言内容数组，格式：['zh' => [...], 'en' => [...], ...]
     */
    public function generateMultilingualContent(
        string $keyword,
        string $categoryName,
        array $tags,
        array $languages,
        string $rules
    ): array {
        $result = [];

        // 优先生成简体中文（cn），确保至少有一个语言版本成功
        $sortedLanguages = [];
        $cnIndex = array_search('cn', $languages);
        if ($cnIndex !== false) {
            $sortedLanguages[] = $languages[$cnIndex];
            foreach ($languages as $index => $lang) {
                if ($index !== $cnIndex) {
                    $sortedLanguages[] = $lang;
                }
            }
        } else {
            // 如果没有cn，检查是否有zh（兼容旧代码）
            $zhIndex = array_search('zh', $languages);
            if ($zhIndex !== false) {
                $sortedLanguages[] = $languages[$zhIndex];
                foreach ($languages as $index => $lang) {
                    if ($index !== $zhIndex) {
                        $sortedLanguages[] = $lang;
                    }
                }
            } else {
                $sortedLanguages = $languages;
            }
        }

        foreach ($sortedLanguages as $lang) {
            try {
                // 构建提示词
                $prompt = $this->buildPrompt($keyword, $categoryName, $tags, $rules, $lang);

                // 构建消息
                $messages = [
                    [
                        'role' => 'system',
                        'content' => '你是一位专业的博客文章写作专家，擅长创作高质量的技术文章。请确保生成的内容使用HTML格式，包含适当的HTML标签（如<p>、<h2>、<ul>、<li>、<strong>等），适合TinyMCE富文本编辑器直接使用。',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ];

                // 调用 API
                $content = $this->generateContent($messages);

                $result[$lang] = $content;

                // 每次请求间隔 2 秒，避免速率限制
                if (count($sortedLanguages) > 1 && $lang !== $sortedLanguages[count($sortedLanguages) - 1]) {
                    sleep(2);
                }
            } catch (\Exception $e) {
                Log::error("生成 {$lang} 语言内容失败", [
                    'keyword' => $keyword,
                    'error' => $e->getMessage(),
                ]);
                // 如果简体中文（cn或zh）生成失败，抛出异常；其他语言失败则继续
                if ($lang === 'cn' || $lang === 'zh' || empty($result)) {
                    throw $e;
                }
                // 其他语言失败时，如果已经有成功的结果，继续处理下一个语言
            }
        }

        return $result;
    }

    /**
     * 清理 JSON 字符串，移除可能导致解析失败的控制字符
     *
     * @param string $jsonString JSON 字符串
     * @return string 清理后的 JSON 字符串
     */
    private function cleanJsonString(string $jsonString): string
    {
        // 移除 BOM 标记
        $jsonString = preg_replace('/^\xEF\xBB\xBF/', '', $jsonString);
        
        // 确保字符串是有效的 UTF-8
        if (!mb_check_encoding($jsonString, 'UTF-8')) {
            $jsonString = mb_convert_encoding($jsonString, 'UTF-8', 'UTF-8');
        }
        
        // JSON 规范要求字符串值中的控制字符必须转义
        // 移除所有未转义的控制字符（0x00-0x1F, 0x7F）
        // 但保留 JSON 结构字符（空格等）
        $jsonString = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $jsonString);
        
        return $jsonString;
    }

    /**
     * 更激进地清理 JSON 字符串（用于解析失败时的备用方案）
     *
     * @param string $jsonString JSON 字符串
     * @return string 清理后的 JSON 字符串
     */
    private function cleanJsonStringAggressive(string $jsonString): string
    {
        // 先进行基本清理
        $jsonString = $this->cleanJsonString($jsonString);
        
        // 更激进的处理：移除所有控制字符，包括 LF、CR、TAB
        // 因为这些在实际的 JSON 字符串值中应该被转义为 \n, \r, \t
        $jsonString = preg_replace('/[\x00-\x1F\x7F]/', '', $jsonString);
        
        // 移除零宽字符
        $jsonString = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $jsonString);
        
        // 确保字符串以 { 开头，以 } 结尾（至少尝试找到有效的 JSON）
        if (!preg_match('/^\s*\{/', $jsonString)) {
            if (preg_match('/\{.*\}/s', $jsonString, $matches)) {
                $jsonString = $matches[0];
            }
        }
        
        return $jsonString;
    }

    /**
     * 修复被截断的 JSON（尝试智能补全）
     *
     * @param string $jsonString JSON 字符串
     * @param bool $isTruncated 是否已知被截断
     * @return string 修复后的 JSON 字符串
     */
    private function fixTruncatedJson(string $jsonString, bool $isTruncated = false): string
    {
        $original = $jsonString;
        
        // 检查括号平衡
        $openBraces = substr_count($jsonString, '{');
        $closeBraces = substr_count($jsonString, '}');
        $openBrackets = substr_count($jsonString, '[');
        $closeBrackets = substr_count($jsonString, ']');
        
        // 检查引号平衡（需要排除转义的引号）
        $quotes = 0;
        $inString = false;
        $escapeNext = false;
        for ($i = 0; $i < strlen($jsonString); $i++) {
            $char = $jsonString[$i];
            if ($escapeNext) {
                $escapeNext = false;
                continue;
            }
            if ($char === '\\') {
                $escapeNext = true;
                continue;
            }
            if ($char === '"') {
                $inString = !$inString;
                $quotes++;
            }
        }
        
        // 如果最后一个字符不是 } 或 ]，可能被截断
        $lastChar = rtrim($jsonString);
        $lastChar = $lastChar !== '' ? substr($lastChar, -1) : '';
        
        // 如果已知被截断或括号不平衡，尝试补全
        if ($isTruncated || $openBraces > $closeBraces || $openBrackets > $closeBrackets) {
            // 如果字符串未闭合，先闭合字符串
            if ($inString && $lastChar !== '"') {
                $jsonString .= '"';
            }
            
            // 补全数组
            if ($openBrackets > $closeBrackets) {
                $jsonString .= str_repeat(']', $openBrackets - $closeBrackets);
            }
            
            // 补全对象
            if ($openBraces > $closeBraces) {
                $jsonString .= str_repeat('}', $openBraces - $closeBraces);
            }
        }
        
        // 如果补全后与原来相同，说明可能是其他问题
        return $jsonString;
    }
}

