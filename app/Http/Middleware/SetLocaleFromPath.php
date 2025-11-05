<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;

class SetLocaleFromPath
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 从cookie读取语言设置，如果没有则使用默认语言
        $langCode = $request->cookie('locale', 'cn');
        
        // 语言代码映射：cookie中的语言代码 -> Laravel locale
        $langMap = [
            'cn' => 'zh',  // 中文简体
            'zh' => 'zh',  // 中文简体（备用）
            'tw' => 'tw',  // 中文繁体
            'en' => 'en',  // 英文
        ];
        
        // 获取对应的locale，如果不存在则使用'zh'作为默认值
        $locale = $langMap[$langCode] ?? 'zh';
        App::setLocale($locale);
        
        // 保存当前语言代码到请求属性中，供视图使用
        $request->attributes->set('current_lang', $langCode);
        
        return $next($request);
    }
}


