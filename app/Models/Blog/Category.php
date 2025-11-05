<?php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'blog_categories';

    protected $fillable = [
        'parent_id', 'sort', 'visible',
        'name', 'slug', 'description',
    ];

    protected $casts = [
        'visible' => 'boolean',
        'name' => 'array',
        'description' => 'array',
    ];
}


