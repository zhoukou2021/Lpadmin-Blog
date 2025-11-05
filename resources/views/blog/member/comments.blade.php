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
                <div class="comment-content" style="flex:1;">
                  <div class="comment-header">
                    <span class="comment-author">{{ $user->nickname }}</span>
                    <span class="comment-time">{{ $comment->created_at->format('Y-m-d H:i') }}</span>
                  </div>
                  <div class="comment-text">{{ $comment->content }}</div>
                  @if($post)
                    <div class="comment-post-link" style="margin-top:8px;padding:8px;background:#f5f5f5;border-radius:6px;">
                      <i class="bi bi-file-text"></i>
                      <a href="{{ blog_url($post['slug']) }}" style="color:var(--primary);text-decoration:none;margin-left:6px;">{{ $post['title'] }}</a>
                    </div>
                  @endif
                  @if($comment->status === 'pending')
                    <div style="margin-top:8px;">
                      <span style="padding:4px 8px;background:#fff3cd;color:#856404;border-radius:4px;font-size:12px;">{{ __('blog.comment_pending') }}</span>
                    </div>
                  @elseif($comment->status === 'rejected')
                    <div style="margin-top:8px;">
                      <span style="padding:4px 8px;background:#f8d7da;color:#721c24;border-radius:4px;font-size:12px;">{{ __('blog.comment_rejected') }}</span>
                    </div>
                  @endif
                </div>
              </div>
            @endforeach
          </div>

          <!-- 无限滚动加载器 -->
          @if($comments->hasMorePages())
            <div id="infiniteScrollLoader" class="infinite-scroll-loader" style="display:none;padding:20px;text-align:center;border-top:1px solid #e6eee6;margin-top:20px;">
              <div style="color:#999;"><i class="bi bi-arrow-repeat" style="animation:spin 1s linear infinite;display:inline-block;margin-right:8px;"></i>加载中...</div>
            </div>
            <div id="infiniteScrollEnd" class="infinite-scroll-end" style="display:none;padding:20px;text-align:center;border-top:1px solid #e6eee6;margin-top:20px;color:#999;">没有更多了</div>
          @else
            <div id="infiniteScrollEnd" class="infinite-scroll-end" style="padding:20px;text-align:center;border-top:1px solid #e6eee6;margin-top:20px;color:#999;">没有更多了</div>
          @endif
        @else
          <div style="padding:40px;text-align:center;color:#999;">
            <i class="bi bi-chat-dots" style="font-size:48px;margin-bottom:12px;display:block;opacity:0.5"></i>
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
<style>
  /* 会员中心菜单样式 */
  .member-menu {
    display: flex;
    flex-direction: column;
  }
  
  .member-menu-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    color: var(--ink);
    text-decoration: none;
    border-bottom: 1px solid #f0f0f0;
    transition: all .2s;
  }
  
  .member-menu-item:last-child {
    border-bottom: none;
  }
  
  .member-menu-item:hover {
    background: var(--primary-weak);
    color: var(--primary);
  }
  
  .member-menu-item.active {
    background: var(--primary-weak);
    color: var(--primary);
    font-weight: 600;
  }
  
  .member-menu-item i {
    font-size: 16px;
    width: 20px;
    text-align: center;
  }

  /* 评论列表样式 */
  .comments-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  .comment-item {
    padding: 16px;
    background: #fafafa;
    border: 1px solid #e6eee6;
    border-radius: 8px;
  }

  .comment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
  }

  .comment-author {
    font-weight: 600;
    color: var(--ink);
    font-size: 14px;
  }

  .comment-time {
    font-size: 12px;
    color: #999;
  }

  .comment-text {
    font-size: 14px;
    line-height: 1.6;
    color: var(--ink);
    word-wrap: break-word;
    white-space: pre-wrap;
  }

  .comment-post-link {
    font-size: 13px;
    display: flex;
    align-items: center;
  }

  .comment-post-link a:hover {
    text-decoration: underline;
  }

  .infinite-scroll-loader{animation:fadeIn .3s ease}
  .infinite-scroll-end{animation:fadeIn .3s ease}
  @keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}
  @media (max-width: 767.98px) {
    .infinite-scroll-loader{padding:16px}
    .infinite-scroll-end{padding:16px}
  }
</style>
<script>
  (function(){
    var currentPage = 1;
    var isLoading = false;
    var hasMore = {{ $comments->hasMorePages() ? 'true' : 'false' }};
    var userNickname = '{{ $user->nickname ?? "" }}';
    
    // 渲染评论HTML
    function renderCommentHTML(comment){
      var html = '<div class="comment-item">';
      html += '<div class="comment-content" style="flex:1;">';
      html += '<div class="comment-header">';
      html += '<span class="comment-author">' + userNickname + '</span>';
      html += '<span class="comment-time">' + (comment.created_at || '') + '</span>';
      html += '</div>';
      html += '<div class="comment-text">' + (comment.content || '') + '</div>';
      
      if (comment.post && comment.post.title) {
        var postUrl = comment.post.slug ? '/' + comment.post.slug : '#';
        html += '<div class="comment-post-link" style="margin-top:8px;padding:8px;background:#f5f5f5;border-radius:6px;">';
        html += '<i class="bi bi-file-text"></i>';
        html += '<a href="' + postUrl + '" style="color:var(--primary);text-decoration:none;margin-left:6px;">' + comment.post.title + '</a>';
        html += '</div>';
      }
      
      if (comment.status === 'pending') {
        html += '<div style="margin-top:8px;">';
        html += '<span style="padding:4px 8px;background:#fff3cd;color:#856404;border-radius:4px;font-size:12px;">{{ __("blog.comment_pending") }}</span>';
        html += '</div>';
      } else if (comment.status === 'rejected') {
        html += '<div style="margin-top:8px;">';
        html += '<span style="padding:4px 8px;background:#f8d7da;color:#721c24;border-radius:4px;font-size:12px;">{{ __("blog.comment_rejected") }}</span>';
        html += '</div>';
      }
      
      html += '</div></div>';
      return html;
    }
    
    // 加载更多数据
    function loadMoreComments(){
      if (isLoading || !hasMore) return;
      
      isLoading = true;
      var loader = document.getElementById('infiniteScrollLoader');
      var end = document.getElementById('infiniteScrollEnd');
      if (loader) loader.style.display = 'block';
      if (end) end.style.display = 'none';
      
      currentPage++;
      var url = '{{ route("site.member.comments.loadMore") }}?page=' + currentPage;
      
      fetch(url, {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(function(response){
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(function(data){
        if (data.code === 0 && data.data && data.data.length > 0) {
          var container = document.querySelector('.comments-list');
          if (container) {
            data.data.forEach(function(comment){
              container.insertAdjacentHTML('beforeend', renderCommentHTML(comment));
            });
          }
          hasMore = data.has_more || false;
          if (!hasMore && end) {
            end.style.display = 'block';
          }
        } else {
          hasMore = false;
          if (end) end.style.display = 'block';
        }
      })
      .catch(function(error){
        console.error('Error loading more comments:', error);
        hasMore = false;
        if (end) end.style.display = 'block';
      })
      .finally(function(){
        isLoading = false;
        if (loader) loader.style.display = 'none';
      });
    }
    
    // 无限滚动监听
    function initInfiniteScroll(){
      if (!hasMore) return;
      
      var threshold = 200;
      var ticking = false;
      
      function checkScroll(){
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        var windowHeight = window.innerHeight || document.documentElement.clientHeight;
        var documentHeight = document.documentElement.scrollHeight;
        
        if (scrollTop + windowHeight >= documentHeight - threshold) {
          loadMoreComments();
        }
        ticking = false;
      }
      
      window.addEventListener('scroll', function(){
        if (!ticking) {
          window.requestAnimationFrame(checkScroll);
          ticking = true;
        }
      }, { passive: true });
    }
    
    initInfiniteScroll();
  })();
</script>
@endsection

