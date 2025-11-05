<?php

namespace App\Services;

use App\Helpers\ConfigHelper;
use Illuminate\Support\Facades\Log;

/**
 * 评论内容过滤服务
 * 提供安全过滤和敏感词过滤功能
 */
class CommentFilterService
{
    /**
     * 过滤评论内容
     * 
     * @param string $content 原始评论内容
     * @return array ['content' => string, 'has_sensitive_word' => bool]
     */
    public static function filter(string $content): array
    {
        // 1. 基础安全过滤（去除HTML标签、XSS防护）
        $filtered = self::sanitize($content);
        
        // 2. 敏感词检测
        $sensitiveResult = self::checkSensitiveWords($filtered);
        
        return [
            'content' => $filtered,
            'has_sensitive_word' => $sensitiveResult['has_sensitive_word'],
            'sensitive_words' => $sensitiveResult['sensitive_words'],
        ];
    }

    /**
     * 内容安全过滤（防止XSS攻击）
     * 
     * @param string $content 原始内容
     * @return string 过滤后的内容
     */
    private static function sanitize(string $content): string
    {
        // 先去除HTML标签（保留文本内容）
        $content = strip_tags($content);
        
        // 去除潜在的脚本标签和事件处理器
        $content = preg_replace('/javascript:/i', '', $content);
        $content = preg_replace('/on\w+\s*=/i', '', $content);
        
        // 去除SQL注入风险的特殊字符组合
        $content = preg_replace('/(\s|^)(union|select|insert|update|delete|drop|create|alter|exec|execute)\s+/i', '', $content);
        
        // 去除控制字符（保留换行符和制表符）
        $content = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $content);
        
        // HTML实体编码（防止XSS，但保留换行等基本格式）
        // 注意：这里不进行HTML实体编码，因为前端显示时需要正常显示文本
        // 如果需要存储HTML实体编码的内容，可以在这里启用
        // $content = htmlspecialchars($content, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
        
        // 清理多余的空格（保留单个空格和换行）
        $content = preg_replace('/[ \t]+/', ' ', $content); // 多个空格/制表符合并为单个空格
        $content = preg_replace('/\n\s*\n\s*\n+/', "\n\n", $content); // 多个换行合并为最多两个换行
        
        return trim($content);
    }

    /**
     * 检测敏感词
     * 
     * @param string $content 要检测的内容
     * @return array ['has_sensitive_word' => bool, 'sensitive_words' => array]
     */
    private static function checkSensitiveWords(string $content): array
    {
        // 从系统配置中获取敏感词列表
        $sensitiveWordsConfig = ConfigHelper::get('sensitive_word', '');
        
        if (empty($sensitiveWordsConfig)) {
            return [
                'has_sensitive_word' => false,
                'sensitive_words' => [],
            ];
        }

        // 使用 | 分割敏感词
        $sensitiveWords = array_filter(
            array_map('trim', explode('|', $sensitiveWordsConfig)),
            function($word) {
                return !empty($word);
            }
        );

        if (empty($sensitiveWords)) {
            return [
                'has_sensitive_word' => false,
                'sensitive_words' => [],
            ];
        }

        $foundWords = [];
        $contentLower = mb_strtolower($content, 'UTF-8');

        // 检测敏感词（不区分大小写）
        foreach ($sensitiveWords as $word) {
            $wordLower = mb_strtolower($word, 'UTF-8');
            if (mb_strpos($contentLower, $wordLower) !== false) {
                $foundWords[] = $word;
            }
        }

        return [
            'has_sensitive_word' => !empty($foundWords),
            'sensitive_words' => $foundWords,
        ];
    }

    /**
     * 替换敏感词（可选功能）
     * 
     * @param string $content 原始内容
     * @param string $replacement 替换字符，默认为 *
     * @return string 替换后的内容
     */
    public static function replaceSensitiveWords(string $content, string $replacement = '*'): string
    {
        $sensitiveWordsConfig = ConfigHelper::get('sensitive_word', '');
        
        if (empty($sensitiveWordsConfig)) {
            return $content;
        }

        $sensitiveWords = array_filter(
            array_map('trim', explode('|', $sensitiveWordsConfig)),
            function($word) {
                return !empty($word);
            }
        );

        if (empty($sensitiveWords)) {
            return $content;
        }

        $filtered = $content;
        foreach ($sensitiveWords as $word) {
            $len = mb_strlen($word, 'UTF-8');
            $replaceStr = str_repeat($replacement, $len);
            $filtered = preg_replace('/' . preg_quote($word, '/') . '/iu', $replaceStr, $filtered);
        }

        return $filtered;
    }
}

