<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * 切换语言
     */
    public function switch(Request $request)
    {
        $lang = $request->input('lang', 'cn');
        
        // 验证语言代码
        $allowedLangs = ['cn', 'en', 'tw'];
        if (!in_array($lang, $allowedLangs)) {
            $lang = 'cn';
        }
        
        // 返回上一页或首页，并设置cookie
        $redirectUrl = $request->input('redirect', '/');
        
        return redirect($redirectUrl)->cookie('locale', $lang, 60 * 24 * 30); // 30天有效期
    }
}

