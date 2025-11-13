@extends('blog.layouts.main')

@section('banner')
  @include('blog.components.banner', ['banner' => $tagBanner ?? null, 'cssClass' => 'tag-banner'])
@endsection

@section('content')

  <div class="row layout-main">
    <div class="col-lg-9 mb-3">
      <div class="card">
        <div class="card-hd">
          <div class="tag-header">
            <div class="tag-info">
              <h2 class="tag-title">{{ $tagName }}</h2>
            </div>
            <div class="view-switcher">
              <button class="view-btn active" data-view="list" title="列表布局">
                <i class="bi bi-list-ul"></i>
              </button>
              <button class="view-btn" data-view="grid" title="瀑布流布局">
                <i class="bi bi-grid-3x3-gap"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="card-bd">
          <div class="posts-container view-list">
            @forelse($posts as $post)
              <div class="post-card">
                @if(!empty($post['cover']))
                  <div class="post-cover">
                    <a href="{{ blog_url($post['slug']) }}"><img src="{{ $post['cover'] }}" alt="{{ $post['title'] }}"></a>
                  </div>
                @endif
                <div class="post-content">
                  <div class="post-title"><a href="{{ blog_url($post['slug']) }}">{{ $post['title'] }}</a></div>
                  <div class="post-summary" data-view-hide="grid">{{ $post['summary'] }}</div>
                  <div class="post-meta">
                    @if(!empty($post['category_name']))
                      <a href="{{ blog_url($post['category_slug']) }}" class="post-category">
                        <i class="bi bi-folder"></i> {{ $post['category_name'] }}
                      </a>
                    @endif
                    <span class="post-meta-item">
                      <i class="bi bi-calendar3"></i> {{ $post['published_at'] ? \Carbon\Carbon::parse($post['published_at'])->format('Y-m-d') : '' }}
                    </span>
                    <span class="post-meta-item">
                      <i class="bi bi-eye"></i> {{ $post['view_count'] ?? 0 }}
                    </span>
                    <span class="post-meta-item">
                      <i class="bi bi-chat-dots"></i> {{ $post['comments_count'] ?? 0 }}
                    </span>
                    <span class="post-meta-item post-like-item" data-post-id="{{ $post['id'] }}">
                      <i class="bi bi-hand-thumbs-up"></i> <span class="likes-count">{{ $post['likes_count'] ?? 0 }}</span>
                    </span>
                    <span class="post-meta-item post-favorite-item" data-post-id="{{ $post['id'] }}">
                      <i class="bi bi-star"></i> <span class="favorites-count">{{ $post['favorites_count'] ?? 0 }}</span>
                    </span>
                  </div>
                  @if(!empty($post['tags']) && count($post['tags']) > 0)
                    <div class="post-tags">
                      @foreach($post['tags'] as $tagItem)
                        <a href="{{ blog_url($tagItem['slug']) }}" class="post-tag">#{{ $tagItem['name'] }}</a>
                      @endforeach
                    </div>
                  @endif
                </div>
              </div>
            @empty
              <div class="muted empty-state">{{ __('blog.no_posts') }}</div>
            @endforelse
          </div>
          <!-- 无限滚动加载提示 -->
          @if($postsRaw->hasMorePages())
            <div id="infiniteScrollLoader" class="infinite-scroll-loader">
              <div class="loading-spinner"></div>
              <div class="loading-text muted">{{ __('blog.loading') ?? '加载中...' }}</div>
            </div>
            <div id="infiniteScrollEnd" class="infinite-scroll-end">
              <div class="muted">{{ __('blog.no_more_posts') ?? '没有更多了' }}</div>
            </div>
          @else
            <div id="infiniteScrollEnd" class="infinite-scroll-end">
              <div class="muted">{{ __('blog.no_more_posts') ?? '没有更多了' }}</div>
            </div>
          @endif
        </div>
      </div>
    </div>

    @include('blog.components.sidebar')
  </div>
@endsection

@section('head')
  @include('blog.components.banner-styles', ['cssClass' => 'tag-banner'])
  <!-- 标签页面样式 -->
  <link rel="stylesheet" href="/static/blog/css/tag.css" />
  <!-- 标签页面配置（供 JavaScript 使用） -->
<script>
    window.BlogTagConfig = {
      tagSlug: '{{ $tagSlug ?? ($tag->slug ?? ("tags_".$tag->id)) }}',
      loadMoreUrl: '{{ route("site.tag.loadMore", ["slug" => ":slug"]) }}',
      hasMore: {{ $postsRaw->hasMorePages() ? 'true' : 'false' }}
    };
</script>
  <!-- 标签页面脚本 -->
  <script src="/static/blog/js/tag.js"></script>
@endsection

