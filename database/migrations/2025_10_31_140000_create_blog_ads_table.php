<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_ads', function (Blueprint $table) {
            $table->id();
            $table->json('title')->comment('多语言标题');
            $table->json('content')->nullable()->comment('多语言内容');
            $table->string('link', 255)->nullable()->comment('链接');
            $table->unsignedTinyInteger('type')->default(1)->comment('1=顶部焦点图,2=友情链接');
            $table->string('image', 255)->nullable()->comment('图片');
            $table->integer('sort')->default(0);
            $table->boolean('visible')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_ads');
    }
};


