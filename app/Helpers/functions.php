<?php

if (!function_exists('lpadmin_route_prefix')) {
    /**
     * 获取LPadmin后台路由前缀（不带斜杠）
     */
    function lpadmin_route_prefix(): string
    {
        return config('lpadmin.route.prefix', 'lpadmin');
    }
}

if (!function_exists('lpadmin_url_prefix')) {
    /**
     * 获取LPadmin后台URL前缀（带前导斜杠）
     */
    function lpadmin_url_prefix(): string
    {
        return '/' . lpadmin_route_prefix();
    }
}

if (!function_exists('enabled_langs')) {
    /**
     * 从系统配置获取启用的语言
     * 返回 [codes => ['cn','en','tw'], labels => ['cn'=>'中文','en'=>'English','tw'=>'繁體中文']]
     */
    function enabled_langs(): array
    {
        try {
            $langValue = \App\Helpers\ConfigHelper::get('lang', '');
            $codes = $langValue ? array_values(array_filter(array_map('trim', explode(',', (string)$langValue)))) : ['cn','en'];
            
            // 获取语言标签配置
            $labels = [];
            $option = \App\Models\LPadmin\Option::where('name', 'lang')->first();
            if ($option && !empty($option->options)) {
                $decoded = json_decode($option->options, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $labels = $decoded;
                }
            }
            
            return ['codes' => $codes, 'labels' => $labels];
        } catch (\Throwable $e) {
            return ['codes' => ['cn','en','tw'], 'labels' => []];
        }
    }
}
if (!function_exists('shellToRegex')) {
    // 将shell通配符模式转换为正则表达式
    function shellToRegex($pattern) {
        $pattern = str_replace('.', '\.', $pattern);
        $pattern = str_replace('*', '.*', $pattern);
        $pattern = str_replace('?', '.', $pattern);
        return '/^' . $pattern . '$/';
    }
}

if (!function_exists('get_i18n_value')) {
    /**
     * 获取多语言字段值
     * @param mixed $data 可能是数组、对象或字符串
     * @param string $lang 当前语言代码
     * @param mixed $default 默认值
     * @return mixed
     */
    function get_i18n_value($data, string $lang, $default = '') {
        if (is_array($data)) {
            return $data[$lang] ?? (array_values($data)[0] ?? $default);
        }
        if (is_object($data) && method_exists($data, 'toArray')) {
            $arr = $data->toArray();
            return $arr[$lang] ?? (array_values($arr)[0] ?? $default);
        }
        if (is_string($data)) {
            // 尝试解析JSON
            $decoded = json_decode($data, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded[$lang] ?? (array_values($decoded)[0] ?? $default);
            }
            return $data ?: $default;
        }
        return $default;
    }
}

if (!function_exists('blog_url')) {
    /**
     * 生成博客URL（统一无语言前缀）
     * @param string $slug 文章/分类/标签的slug
     * @return string
     */
    function blog_url(string $slug): string {
        return '/' . ltrim($slug, '/');
    }
}

if (!function_exists('get_blog_ad')) {
    /**
     * 获取博客广告
     * @param int $type 广告类型（1=顶部焦点图, 2=友情链接, 3=分类页banner, 4=标签页banner, 5=博客详情页banner, 6=代码广告, 7=会员中心banner）
     * @param string|null $lang 当前语言代码，用于处理多语言标题（默认从request获取）
     * @param bool $single 是否只获取一个（true返回单个数组，false返回数组列表）
     * @param int|null $limit 获取数量限制（仅在single=false时有效）
     * @return array|null single=true时返回单个广告数组或null，single=false时返回广告数组列表
     */
    function get_blog_ad(int $type, ?string $lang = null, bool $single = true, ?int $limit = null) {
        // 如果没有提供语言，尝试从request获取
        if ($lang === null) {
            try {
                $request = request();
                $lang = $request->attributes->get('current_lang') ?? $request->cookie('locale', 'cn');
            } catch (\Throwable $e) {
                $lang = 'cn';
            }
        }
        
        $query = \App\Models\Blog\Ad::query()
            ->where('visible', true)
            ->where('type', $type)
            ->orderByDesc('sort')
            ->orderBy('id');
        
        if ($single) {
            $ad = $query->first();
            if (!$ad) {
                return null;
            }
            
            // 处理多语言标题
            $title = is_array($ad->title) 
                ? ($ad->title[$lang] ?? (array_values($ad->title)[0] ?? '')) 
                : ($ad->title ?? '');
            
            // 处理多语言内容
            $content = is_array($ad->content) 
                ? ($ad->content[$lang] ?? (array_values($ad->content)[0] ?? '')) 
                : ($ad->content ?? '');
            
            return [
                'img' => $ad->image ?? '',
                'link' => $ad->link ?? '',
                'title' => $title,
                'content' => $content,
            ];
        } else {
            if ($limit !== null) {
                $query->limit($limit);
            }
            $ads = $query->get();
            
            return $ads->map(function($ad) use ($lang) {
                // 处理多语言标题
                $title = is_array($ad->title) 
                    ? ($ad->title[$lang] ?? (array_values($ad->title)[0] ?? '')) 
                    : ($ad->title ?? '');
                
                // 处理多语言内容
                $content = is_array($ad->content) 
                    ? ($ad->content[$lang] ?? (array_values($ad->content)[0] ?? '')) 
                    : ($ad->content ?? '');
                
                return [
                    'img' => $ad->image ?? '',
                    'link' => $ad->link ?? '',
                    'title' => $title,
                    'content' => $content,
                ];
            })->toArray();
        }
    }
}

if (!function_exists('get_blog_friend_links')) {
    /**
     * 获取友情链接（type=2的广告列表）
     * @param string|null $lang 当前语言代码
     * @param int $limit 数量限制
     * @return array 友情链接数组列表
     */
    function get_blog_friend_links(?string $lang = null, int $limit = 50): array {
        return get_blog_ad(\App\Models\Blog\Ad::TYPE_FRIENDLY_LINK, $lang, false, $limit);
    }
}

