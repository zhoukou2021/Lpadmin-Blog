<?php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nav extends Model
{
    use HasFactory;

    protected $table = 'blog_navs';

    protected $fillable = [
        'title', 'url', 'sort', 'visible', 'icon', 'parent_id',
    ];

    protected $casts = [
        'title' => 'array',
        'visible' => 'boolean',
    ];
}


