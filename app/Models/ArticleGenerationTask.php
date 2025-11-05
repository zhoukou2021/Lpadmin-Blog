<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleGenerationTask extends Model
{
    use HasFactory;

    protected $table = 'article_generation_tasks';

    protected $fillable = [
        'task_id',
        'status',
        'progress',
        'total',
        'success',
        'failed',
        'logs',
        'posts',
        'error',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'logs' => 'array',
        'posts' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
}
