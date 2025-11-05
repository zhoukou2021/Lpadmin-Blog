<?php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $table = 'blog_favorites';

    protected $fillable = [
        'post_id', 'user_id', 'ip', 'ua',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\LPadmin\User::class, 'user_id');
    }
}


