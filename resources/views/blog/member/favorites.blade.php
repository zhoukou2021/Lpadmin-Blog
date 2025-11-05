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
      <div class="card-hd">{{ __('blog.my_favorites') }}</div>
      <div class="card-bd">
        @if($posts && $posts->count() > 0)
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
                      <i class="bi bi-hand-thumbs-up"></i> <span class="likes-count">{{ $post['likes_count'] ?? 0 }}</span>
                    </div>
                    <div class="post-meta-item post-favorite-item" data-post-id="{{ $post['id'] }}" style="cursor:pointer;user-select:none;">
                      <i class="bi bi-star-fill"></i> <span class="favorites-count">{{ $post['favorites_count'] ?? 0 }}</span>
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
          @if($favorites->hasMorePages())
            <div id="infiniteScrollLoader" class="infinite-scroll-loader" style="display:none;padding:20px;text-align:center;border-top:1px solid #e6eee6;margin-top:20px;">
              <div style="color:#999;"><i class="bi bi-arrow-repeat" style="animation:spin 1s linear infinite;display:inline-block;margin-right:8px;"></i>加载中...</div>
            </div>
            <div id="infiniteScrollEnd" class="infinite-scroll-end" style="display:none;padding:20px;text-align:center;border-top:1px solid #e6eee6;margin-top:20px;color:#999;">没有更多了</div>
          @else
            <div id="infiniteScrollEnd" class="infinite-scroll-end" style="padding:20px;text-align:center;border-top:1px solid #e6eee6;margin-top:20px;color:#999;">没有更多了</div>
          @endif
        @else
          <div style="padding:40px;text-align:center;color:#999;">
            <i class="bi bi-star" style="font-size:48px;margin-bottom:12px;display:block;opacity:0.5"></i>
            <div>{{ __('blog.no_favorites') }}</div>
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
    var hasMore = {{ $favorites->hasMorePages() ? 'true' : 'false' }};
    
    // 渲染文章卡片HTML
    function renderPostHTML(post){
      var html = '<div class="post-card">';
      
      var postUrl = post.slug ? '/' + post.slug : '#';
      if (post.cover) {
        html += '<div class="post-cover"><a href="' + postUrl + '"><img src="' + post.cover + '" alt="' + (post.title || '') + '"></a></div>';
      }
      
      html += '<div class="post-content">';
      html += '<h3 class="post-title"><a href="' + postUrl + '">' + (post.title || '') + '</a></h3>';
      
      if (post.summary) {
        html += '<div class="post-summary">' + (post.summary || '') + '</div>';
      }
      
      html += '<div class="post-meta">';
      if (post.category_name) {
        var categoryUrl = post.category_slug ? '/' + post.category_slug : '#';
        html += '<a href="' + categoryUrl + '" class="post-category"><i class="bi bi-folder"></i> ' + post.category_name + '</a>';
      }
      
      var publishedDate = '';
      if (post.published_at) {
        var date = new Date(post.published_at);
        publishedDate = date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');
      }
      html += '<div class="post-meta-item"><i class="bi bi-calendar3"></i> ' + publishedDate + '</div>';
      html += '<div class="post-meta-item"><i class="bi bi-eye"></i> ' + (post.view_count || 0) + '</div>';
      html += '<div class="post-meta-item"><i class="bi bi-chat-dots"></i> ' + (post.comments_count || 0) + '</div>';
      html += '<div class="post-meta-item post-like-item" data-post-id="' + (post.id || '') + '" style="cursor:pointer;user-select:none;"><i class="bi bi-hand-thumbs-up"></i> <span class="likes-count">' + (post.likes_count || 0) + '</span></div>';
      html += '<div class="post-meta-item post-favorite-item" data-post-id="' + (post.id || '') + '" style="cursor:pointer;user-select:none;"><i class="bi bi-star-fill"></i> <span class="favorites-count">' + (post.favorites_count || 0) + '</span></div>';
      html += '</div>';
      
      if (post.tags && post.tags.length > 0) {
        html += '<div class="post-tags">';
        post.tags.forEach(function(tag){
          var tagUrl = tag.slug ? '/' + tag.slug : '#';
          html += '<a href="' + tagUrl + '" class="post-tag">#' + (tag.name || '') + '</a>';
        });
        html += '</div>';
      }
      
      html += '</div></div>';
      return html;
    }
    
    // 加载更多数据
    function loadMorePosts(){
      if (isLoading || !hasMore) return;
      
      isLoading = true;
      var loader = document.getElementById('infiniteScrollLoader');
      var end = document.getElementById('infiniteScrollEnd');
      if (loader) loader.style.display = 'block';
      if (end) end.style.display = 'none';
      
      currentPage++;
      var url = '{{ route("site.member.favorites.loadMore") }}?page=' + currentPage;
      
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
          var container = document.querySelector('.posts-container');
          if (container) {
            data.data.forEach(function(post){
              container.insertAdjacentHTML('beforeend', renderPostHTML(post));
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
        console.error('Error loading more posts:', error);
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
          loadMorePosts();
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

