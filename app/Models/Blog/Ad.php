<?php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    // 广告类型常量
    const TYPE_TOP_BANNER = 1;      // 顶部焦点图
    const TYPE_FRIENDLY_LINK = 2;   // 友情链接
    const TYPE_CATEGORY_BANNER = 3; // 分类页banner
    const TYPE_TAG_BANNER = 4;      // tag页banner
    const TYPE_POST_BANNER = 5;     // 博客详情页banner
    const TYPE_CODE_AD = 6;         // 代码广告
    const TYPE_MEMBER_CENTER = 7;         // 会员中心

    // 广告类型标签映射
    const TYPE_LABELS = [
        self::TYPE_TOP_BANNER => '顶部焦点图',
        self::TYPE_FRIENDLY_LINK => '友情链接',
        self::TYPE_CATEGORY_BANNER => '分类页banner',
        self::TYPE_TAG_BANNER => 'tag页banner',
        self::TYPE_POST_BANNER => '博客详情页banner',
        self::TYPE_CODE_AD => '代码广告',
        self::TYPE_MEMBER_CENTER => '会员中心',
    ];

    // 获取所有类型选项（用于下拉框）
    public static function getTypeOptions(): array
    {
        return self::TYPE_LABELS;
    }

    // 获取类型标签
    public static function getTypeLabel(int $type): string
    {
        return self::TYPE_LABELS[$type] ?? '未知类型';
    }

    protected $table = 'blog_ads';

    protected $fillable = [
        'title', 'content', 'link', 'type', 'image', 'sort', 'visible',
    ];

    protected $casts = [
        'title' => 'array',
        'content' => 'array',
        'visible' => 'boolean',
        'type' => 'integer',
        'sort' => 'integer',
    ];
}


