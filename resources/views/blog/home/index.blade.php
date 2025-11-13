@extends('blog.layouts.main')

@section('banner')
  @include('blog.components.banner-3d', ['banners' => $banners ?? [], 'bannerId' => 'banner'])
@endsection

@section('content')
  @php
    $seo = $seo ?? ['title' => __('blog.blog')];
  @endphp

  <div class="row layout-main">
    <div class="col-lg-9 mb-3">
      <div class="card">
        <div class="card-hd">
          <div class="tabs">
            <a href="javascript:;" data-tab="new" class="active">{{ __('blog.latest') }}</a>
            <a href="javascript:;" data-tab="hot">{{ __('blog.hot') }}</a>
            <a href="javascript:;"  data-tab="rec">{{ __('blog.recommended') }}</a>
          </div>
        </div>
        <div class="card-bd">
          <div class="tab-pane" id="tab-new">
            <div class="posts-container" data-type="new">
            @foreach(($latest ?? []) as $post)
              <div class="post-card">
                @if(!empty($post['cover']))
                  <div class="post-cover">
                    <a href="{{ blog_url($post['slug']) }}"><img src="{{ $post['cover'] }}" alt="{{ $post['title'] }}"></a>
                  </div>
                @endif
                <div class="post-content">
                  <div class="post-title"><a href="{{ blog_url($post['slug']) }}">{{ $post['title'] }}</a></div>
                  @if(!empty($post['summary']))
                    <div class="post-summary">{{ $post['summary'] }}</div>
                  @endif
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
                      @foreach($post['tags'] as $tag)
                        <a href="{{ blog_url($tag['slug']) }}" class="post-tag">#{{ $tag['name'] }}</a>
                      @endforeach
                    </div>
                  @endif
                </div>
              </div>
            @endforeach
            </div>
            <!-- 无限滚动加载提示 -->
            @if(count($latest ?? []) >= 10)
              <div class="infinite-scroll-loader" data-type="new">
                <div class="loading-spinner"></div>
                <div class="loading-text muted">加载中...</div>
              </div>
              <div class="infinite-scroll-end" data-type="new">
                <div class="muted">没有更多了</div>
              </div>
            @endif
          </div>
          <div class="tab-pane" id="tab-hot" style="display:none">
            <div class="posts-container" data-type="hot">
            @foreach(($hot ?? []) as $post)
              <div class="post-card">
                @if(!empty($post['cover']))
                  <div class="post-cover">
                    <a href="{{ blog_url($post['slug']) }}"><img src="{{ $post['cover'] }}" alt="{{ $post['title'] }}"></a>
                  </div>
                @endif
                <div class="post-content">
                  <div class="post-title"><a href="{{ blog_url($post['slug']) }}">{{ $post['title'] }}</a></div>
                  @if(!empty($post['summary']))
                    <div class="post-summary">{{ $post['summary'] }}</div>
                  @endif
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
                      @foreach($post['tags'] as $tag)
                        <a href="{{ blog_url($tag['slug']) }}" class="post-tag">#{{ $tag['name'] }}</a>
                      @endforeach
                    </div>
                  @endif
                </div>
              </div>
            @endforeach
            </div>
            <!-- 无限滚动加载提示 -->
            @if(count($hot ?? []) >= 10)
              <div class="infinite-scroll-loader" data-type="hot">
                <div class="loading-spinner"></div>
                <div class="loading-text muted">加载中...</div>
              </div>
              <div class="infinite-scroll-end" data-type="hot">
                <div class="muted">没有更多了</div>
              </div>
            @endif
          </div>
          <div class="tab-pane" id="tab-rec" style="display:none">
            <div class="posts-container" data-type="rec">
            @forelse(($recommended ?? []) as $post)
              <div class="post-card">
                @if(!empty($post['cover']))
                  <div class="post-cover">
                    <a href="{{ blog_url($post['slug']) }}"><img src="{{ $post['cover'] }}" alt="{{ $post['title'] }}"></a>
                  </div>
                @endif
                <div class="post-content">
                  <div class="post-title"><a href="{{ blog_url($post['slug']) }}">{{ $post['title'] }}</a></div>
                  @if(!empty($post['summary']))
                    <div class="post-summary">{{ $post['summary'] }}</div>
                  @endif
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
                      @foreach($post['tags'] as $tag)
                        <a href="{{ blog_url($tag['slug']) }}" class="post-tag">#{{ $tag['name'] }}</a>
                      @endforeach
                    </div>
                  @endif
                </div>
              </div>
            @empty
              <div class="muted">{{ __('blog.no_recommended') }}</div>
            @endforelse
            </div>
            <!-- 无限滚动加载提示 -->
            @if(count($recommended ?? []) >= 10)
              <div class="infinite-scroll-loader" data-type="rec">
                <div class="loading-spinner"></div>
                <div class="loading-text muted">加载中...</div>
              </div>
              <div class="infinite-scroll-end" data-type="rec">
                <div class="muted">没有更多了</div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    @include('blog.components.sidebar')
  </div>
@endsection

@section('head')
  @include('blog.components.banner-3d-styles')
  @include('blog.components.banner-3d-script', ['banners' => $banners ?? [], 'bannerId' => 'banner'])
  <!-- 首页样式 -->
  <link rel="stylesheet" href="/static/blog/css/home.css" />
  <!-- 首页配置（供 JavaScript 使用） -->
  <script>
    window.BlogHomeConfig = {
      loadMoreUrl: '{{ route("site.home.loadMore") }}',
      hasMoreNew: {{ count($latest ?? []) >= 10 ? 'true' : 'false' }},
      hasMoreHot: {{ count($hot ?? []) >= 10 ? 'true' : 'false' }},
      hasMoreRec: {{ count($recommended ?? []) >= 10 ? 'true' : 'false' }}
      };
  </script>
  <!-- 首页脚本 -->
  <script src="/static/blog/js/home.js"></script>
@endsection


