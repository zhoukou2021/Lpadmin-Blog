@extends('blog.layouts.main')

@section('banner')
  @include('blog.components.banner', ['banner' => $memberBanner ?? null, 'cssClass' => 'member-banner'])
@endsection

@section('content')
<div class="layout-main row">
  <div class="col-lg-3">
    <!-- 会员中心左侧菜单 -->
    <div class="card" style="margin-bottom:12px">
      <div class="card-hd">{{ __('blog.member_center') }}</div>
      <div class="card-bd" style="padding:0;">
        <div class="member-menu">
          <a href="{{ route('site.member.index') }}" class="member-menu-item {{ request()->routeIs('site.member.index') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i>
            <span>{{ __('blog.overview') }}</span>
          </a>
          <a href="{{ route('site.member.profile') }}" class="member-menu-item {{ request()->routeIs('site.member.profile') ? 'active' : '' }}">
            <i class="bi bi-person"></i>
            <span>{{ __('blog.edit_profile') }}</span>
          </a>
          <a href="{{ route('site.member.favorites') }}" class="member-menu-item {{ request()->routeIs('site.member.favorites') ? 'active' : '' }}">
            <i class="bi bi-star"></i>
            <span>{{ __('blog.my_favorites') }}</span>
          </a>
          <a href="{{ route('site.member.likes') }}" class="member-menu-item {{ request()->routeIs('site.member.likes') ? 'active' : '' }}">
            <i class="bi bi-heart"></i>
            <span>{{ __('blog.my_likes') }}</span>
          </a>
          <a href="{{ route('site.member.comments') }}" class="member-menu-item {{ request()->routeIs('site.member.comments') ? 'active' : '' }}">
            <i class="bi bi-chat-dots"></i>
            <span>{{ __('blog.my_comments') }}</span>
          </a>
        </div>
      </div>
    </div>

    <!-- 侧边栏 -->
    @include('blog.partials.sidebar', ['categories' => $categories ?? [], 'tags' => $tags ?? []])
  </div>

  <div class="col-lg-9">
    <div class="card">
      <div class="card-hd">{{ __('blog.my_likes') }}</div>
      <div class="card-bd">
        @if(isset($posts) && $posts->count() > 0)
          <div class="posts-container view-list">
            @foreach($posts as $post)
              <div class="post-card">
                @if(!empty($post['cover']))
                  <div class="post-cover">
                    <a href="{{ blog_url($post['slug']) }}"><img src="{{ $post['cover'] }}" alt="{{ $post['title'] }}"></a>
                  </div>
                @endif
                <div class="post-content">
                  <h3 class="post-title">
                    <a href="{{ blog_url($post['slug']) }}">{{ $post['title'] }}</a>
                  </h3>
                  @if(!empty($post['summary']))
                    <div class="post-summary">{{ $post['summary'] }}</div>
                  @endif
                  <div class="post-meta">
                    @if(!empty($post['category_name']))
                      <a href="{{ blog_url($post['category_slug']) }}" class="post-category">
                        <i class="bi bi-folder"></i> {{ $post['category_name'] }}
                      </a>
                    @endif
                    <div class="post-meta-item">
                      <i class="bi bi-calendar3"></i> {{ $post['published_at'] ? \Carbon\Carbon::parse($post['published_at'])->format('Y-m-d') : '' }}
                    </div>
                    <div class="post-meta-item">
                      <i class="bi bi-eye"></i> {{ $post['view_count'] ?? 0 }}
                    </div>
                    <div class="post-meta-item">
                      <i class="bi bi-chat-dots"></i> {{ $post['comments_count'] ?? 0 }}
                    </div>
                    <div class="post-meta-item post-like-item" data-post-id="{{ $post['id'] }}" style="cursor:pointer;user-select:none;">
                      <i class="bi bi-hand-thumbs-up-fill"></i> <span class="likes-count">{{ $post['likes_count'] ?? 0 }}</span>
                    </div>
                    <div class="post-meta-item post-favorite-item" data-post-id="{{ $post['id'] }}" style="cursor:pointer;user-select:none;">
                      <i class="bi bi-star"></i> <span class="favorites-count">{{ $post['favorites_count'] ?? 0 }}</span>
                    </div>
                  </div>
                  @if(isset($post['tags']) && $post['tags']->count() > 0)
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

          <!-- 无限滚动加载器 -->
          @if($likes->hasMorePages())
            <div id="infiniteScrollLoader" class="infinite-scroll-loader">
              <div class="loading-text"><i class="bi bi-arrow-repeat loading-icon"></i>加载中...</div>
            </div>
            <div id="infiniteScrollEnd" class="infinite-scroll-end">没有更多了</div>
          @else
            <div id="infiniteScrollEnd" class="infinite-scroll-end">没有更多了</div>
          @endif
        @else
          <div class="member-empty-state">
            <i class="bi bi-heart"></i>
            <div>{{ __('blog.no_likes') }}</div>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@section('head')
  @include('blog.components.banner-styles', ['cssClass' => 'member-banner'])
  <link rel="stylesheet" href="/static/blog/css/member.css" />
  <script src="/static/blog/js/member-infinite-scroll.js"></script>
  <script src="/static/blog/js/member-posts.js"></script>
  <script>
    window.MemberPostsConfig = {
      type: 'likes',
      loadMoreUrl: '{{ route("site.member.likes.loadMore") }}',
      hasMore: {{ $likes->hasMorePages() ? 'true' : 'false' }}
    };
  </script>
@endsection

