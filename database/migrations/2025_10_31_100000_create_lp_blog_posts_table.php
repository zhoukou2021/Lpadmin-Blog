<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('author_id')->index();
            $table->unsignedBigInteger('category_id')->default(0)->index();
            $table->unsignedTinyInteger('recommend')->default(0)->index();
            $table->string('status', 20)->default('draft');
            $table->unsignedBigInteger('view_count')->default(0);
            $table->string('cover')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->json('title');
            $table->string('slug')->nullable();
            $table->json('summary')->nullable();
            $table->json('content')->nullable();
            $table->json('meta_title')->nullable();
            $table->json('meta_desc')->nullable();
            $table->json('meta_json')->nullable();
            $table->timestamps();

            $table->index(['author_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};


