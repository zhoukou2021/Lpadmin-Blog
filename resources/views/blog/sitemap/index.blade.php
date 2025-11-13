@extends('blog.layouts.main')

@section('content')
  @php
    $seo = $seo ?? ['title' => __('blog.sitemap_title')];
  @endphp

  <div class="row">
    <div class="col-lg-12 mb-3">
      <div class="card">
        <div class="card-hd">
          <h1 class="sitemap-title">
            <i class="bi bi-diagram-3"></i> {{ __('blog.sitemap_title') }}
          </h1>
        </div>
        <div class="card-bd">
          <!-- 快速导航 -->
          <div class="sitemap-nav">
            <a href="#categories" class="sitemap-nav-link">
              <i class="bi bi-folder"></i> {{ __('blog.category_list') }}
            </a>
            <a href="#tags" class="sitemap-nav-link">
              <i class="bi bi-tags"></i> {{ __('blog.tag_list') }}
            </a>
            <a href="#posts" class="sitemap-nav-link">
              <i class="bi bi-file-text"></i> {{ __('blog.post_list') }}
            </a>
          </div>

          <!-- 分类列表 -->
          <section id="categories" class="sitemap-section">
            <h2 class="sitemap-section-title">
              <i class="bi bi-folder"></i> {{ __('blog.category_list') }} ({{ count($categories) }})
            </h2>
            <div class="sitemap-list">
              @forelse($categories as $category)
                <a href="{{ $category['url'] }}" class="sitemap-tag">
                  <i class="bi bi-folder"></i> {{ $category['name'] }}
                </a>
              @empty
                <div class="muted sitemap-empty">{{ __('blog.no_categories') }}</div>
              @endforelse
            </div>
          </section>

          <!-- 标签列表 -->
          <section id="tags" class="sitemap-section">
            <h2 class="sitemap-section-title">
              <i class="bi bi-tags"></i> {{ __('blog.tag_list') }} ({{ count($tags) }})
            </h2>
            <div class="sitemap-list sitemap-list-tags">
              @forelse($tags as $tag)
                <a href="{{ $tag['url'] }}" class="sitemap-tag">
                  <i class="bi bi-tag"></i> {{ $tag['name'] }}
                </a>
              @empty
                <div class="muted sitemap-empty">{{ __('blog.no_tags') }}</div>
              @endforelse
            </div>
          </section>
          <!-- 文章列表 -->
          <section id="posts" class="sitemap-section">
            <h2 class="sitemap-section-title">
              <i class="bi bi-file-text"></i> {{ __('blog.post_list') }} ({{ count($posts) }})
            </h2>
            <div class="sitemap-posts-grid">
              @forelse($posts as $post)
                <div class="sitemap-post-item">
                  <a href="{{ $post['url'] }}" class="sitemap-post-link">
                    {{ $post['title'] }}
                  </a>
                </div>
              @empty
                <div class="muted sitemap-posts-empty">{{ __('blog.no_posts_list') }}</div>
              @endforelse
            </div>
          </section>

          <!-- 返回顶部按钮 -->
          <div class="sitemap-back-top">
            <a href="#top" class="sitemap-back-top-link">
              <i class="bi bi-arrow-up"></i> {{ __('blog.back_to_top') }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('head')
  <link rel="stylesheet" href="/static/blog/css/sitemap.css" />
@endsection

