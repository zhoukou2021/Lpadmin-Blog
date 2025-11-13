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
      <div class="card-hd">{{ __('blog.my_comments') }}</div>
      <div class="card-bd">
        @if(isset($comments) && $comments->count() > 0)
          <div class="comments-list">
            @foreach($comments as $comment)
              @php
                $post = $processPost($comment->post);
              @endphp
              <div class="comment-item">
                <div class="comment-content">
                  <div class="comment-header">
                    <span class="comment-author">{{ $user->nickname }}</span>
                    <span class="comment-time">{{ $comment->created_at->format('Y-m-d H:i') }}</span>
                  </div>
                  <div class="comment-text">{{ $comment->content }}</div>
                  @if($post)
                    <div class="comment-post-link">
                      <i class="bi bi-file-text"></i>
                      <a href="{{ blog_url($post['slug']) }}">{{ $post['title'] }}</a>
                    </div>
                  @endif
                  @if($comment->status === 'pending')
                    <div class="comment-status">
                      <span class="comment-status-pending">{{ __('blog.comment_pending') }}</span>
                    </div>
                  @elseif($comment->status === 'rejected')
                    <div class="comment-status">
                      <span class="comment-status-rejected">{{ __('blog.comment_rejected') }}</span>
                    </div>
                  @endif
                </div>
              </div>
            @endforeach
          </div>

          <!-- 无限滚动加载器 -->
          @if($comments->hasMorePages())
            <div id="infiniteScrollLoader" class="infinite-scroll-loader">
              <div class="loading-text"><i class="bi bi-arrow-repeat loading-icon"></i>加载中...</div>
            </div>
            <div id="infiniteScrollEnd" class="infinite-scroll-end">没有更多了</div>
          @else
            <div id="infiniteScrollEnd" class="infinite-scroll-end">没有更多了</div>
          @endif
        @else
          <div class="member-empty-state">
            <i class="bi bi-chat-dots"></i>
            <div>{{ __('blog.no_comments') }}</div>
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
  <script src="/static/blog/js/member-comments.js"></script>
  <script>
    window.MemberCommentsConfig = {
      loadMoreUrl: '{{ route("site.member.comments.loadMore") }}',
      hasMore: {{ $comments->hasMorePages() ? 'true' : 'false' }},
      userNickname: '{{ $user->nickname ?? "" }}',
      texts: {
        commentPending: @json(__('blog.comment_pending')),
        commentRejected: @json(__('blog.comment_rejected'))
      }
    };
  </script>
@endsection

