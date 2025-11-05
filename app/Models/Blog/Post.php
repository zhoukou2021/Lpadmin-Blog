<?php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'blog_posts';

    protected $fillable = [
        'author_id', 'status', 'cover', 'published_at', 'category_id', 'recommend',
        'title', 'slug', 'summary', 'content',
        'meta_title', 'meta_desc', 'meta_json',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'recommend' => 'boolean',
        'title' => 'array',
        'summary' => 'array',
        'content' => 'array',
        'meta_title' => 'array',
        'meta_desc' => 'array',
        'meta_json' => 'array',
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'blog_post_tag', 'post_id', 'tag_id')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id');
    }

    public function approvedComments()
    {
        return $this->hasMany(Comment::class, 'post_id')->where('status', 'approved');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'post_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'post_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}


