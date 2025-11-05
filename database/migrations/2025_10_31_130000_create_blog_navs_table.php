<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_navs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('title'); // 多语言标题 {lang:value}
            $table->string('url'); // 绝对或相对路径
            $table->string('icon')->nullable(); // 图标
            $table->integer('sort')->default(0);
            $table->integer('parent_id')->default(0);
            $table->boolean('visible')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_navs');
    }
};


