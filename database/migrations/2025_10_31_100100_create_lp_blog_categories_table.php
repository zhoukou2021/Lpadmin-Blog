<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('parent_id')->default(0)->index();
            $table->integer('sort')->default(0);
            $table->boolean('visible')->default(true);
            $table->json('name');
            $table->string('slug')->nullable();
            $table->json('description')->nullable();
            $table->timestamps();

            $table->index(['visible', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_categories');
    }
};


