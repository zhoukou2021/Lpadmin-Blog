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
                    <span class="post-meta-item post-like-item" data-post-id="{{ $post['id'] }}" style="cursor:pointer;user-select:none;">
                      <i class="bi bi-hand-thumbs-up"></i> <span class="likes-count">{{ $post['likes_count'] ?? 0 }}</span>
                    </span>
                    <span class="post-meta-item post-favorite-item" data-post-id="{{ $post['id'] }}" style="cursor:pointer;user-select:none;">
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
              <div class="muted" style="padding:40px;text-align:center;">{{ __('blog.no_posts') }}</div>
            @endforelse
          </div>
          <!-- 无限滚动加载提示 -->
          @if($postsRaw->hasMorePages())
            <div id="infiniteScrollLoader" class="infinite-scroll-loader" style="display:none;padding:20px;text-align:center;border-top:1px solid #e6eee6;margin-top:20px;">
              <div class="loading-spinner" style="display:inline-block;width:32px;height:32px;border:3px solid #e6eee6;border-top-color:var(--primary);border-radius:50%;animation:spin .8s linear infinite;"></div>
              <div class="loading-text muted" style="margin-top:12px;font-size:14px;">{{ __('blog.loading') ?? '加载中...' }}</div>
            </div>
            <div id="infiniteScrollEnd" class="infinite-scroll-end" style="display:none;padding:20px;text-align:center;border-top:1px solid #e6eee6;margin-top:20px;">
              <div class="muted" style="font-size:14px;">{{ __('blog.no_more_posts') ?? '没有更多了' }}</div>
            </div>
          @else
            <div id="infiniteScrollEnd" class="infinite-scroll-end" style="padding:20px;text-align:center;border-top:1px solid #e6eee6;margin-top:20px;">
              <div class="muted" style="font-size:14px;">{{ __('blog.no_more_posts') ?? '没有更多了' }}</div>
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
<style>

  /* 标签头部布局 */
  .tag-header{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;width:100%;border-bottom: 1px solid var(--primary);
    padding-bottom: 15px;box-sizing:border-box}
  .tag-info{flex:1;min-width:0;width:0;box-sizing:border-box}
  .tag-title{margin:0;font-size:20px;font-weight:700;color:var(--primary);line-height:1.4;word-break:break-word;overflow-wrap:break-word}
  .view-switcher{display:flex;gap:8px;flex-shrink:0;margin-top:2px}
  .view-btn{width:38px;height:38px;border:1px solid #e6eee6;background:#fff;border-radius:8px;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;color:#666;box-shadow:0 1px 3px rgba(0,0,0,.05);flex-shrink:0}
  .view-btn:hover{background:var(--primary-weak);color:var(--primary);border-color:var(--primary);transform:translateY(-1px);box-shadow:0 2px 6px rgba(44,163,106,.15)}
  .view-btn.active{background:var(--primary);color:#fff;border-color:var(--primary);box-shadow:0 2px 8px rgba(44,163,106,.2)}
  .view-btn i{font-size:16px}
  @media (max-width: 767.98px){
    .tag-header{flex-direction:row;align-items:flex-start;gap:12px;padding-bottom:12px;width:100%}
    .tag-info{flex:1;min-width:0;width:0;max-width:none;box-sizing:border-box}
    .tag-title{font-size:18px}
    .view-switcher{margin-top:0;flex-shrink:0}
    .view-btn{width:36px;height:36px}
    .view-btn i{font-size:14px}
  }
  @media (max-width: 575.98px){
    .tag-header{padding-bottom:10px;gap:10px}
    .tag-info{flex:1;min-width:0}
    .tag-title{font-size:16px}
    .view-btn{width:34px;height:34px}
  }

  /* 列表视图（默认） */
  .posts-container.view-list .post-card{display:flex;gap:12px;padding:12px 0;border-bottom:1px dashed #e6eee6}
  .posts-container.view-list .post-card:last-child{border-bottom:none}
  .posts-container.view-list .post-cover{flex-shrink:0;width:120px;height:100px;border-radius:8px;overflow:hidden;background:#f0f0f0}
  .posts-container.view-list .post-cover img{width:100%;height:100%;object-fit:cover}
  .posts-container.view-list .post-content{flex:1;min-width:0}
  @media (max-width: 767.98px){
    .posts-container.view-list .post-card{padding:10px 0;gap:10px;flex-direction:row}
    .posts-container.view-list .post-cover{width:100px;height:80px;flex-shrink:0}
    .posts-container.view-list .post-content{flex:1;min-width:0}
  }
  @media (max-width: 575.98px){
    .posts-container.view-list .post-card{padding:8px 0;gap:8px}
    .posts-container.view-list .post-cover{width:90px;height:70px}
  }

  /* 瀑布流视图 */
  .posts-container.view-grid{display:grid;grid-template-columns:repeat(3, 1fr);gap:16px}
  .posts-container.view-grid .post-card{flex-direction:column;border-bottom:none;padding:0;border:1px solid #e6eee6;border-radius:10px;overflow:hidden;background:#fff;transition:transform .2s,box-shadow .2s}
  .posts-container.view-grid .post-card:hover{transform:translateY(-4px);box-shadow:0 8px 24px rgba(18,38,32,.12)}
  .posts-container.view-grid .post-cover{width:100%;height:180px}
  .posts-container.view-grid .post-content{padding:12px}
  .posts-container.view-grid .post-summary[data-view-hide="grid"]{display:none}
  
  @media (max-width: 991.98px){
    .posts-container.view-grid{grid-template-columns:repeat(2, 1fr);gap:12px}
    .posts-container.view-grid .post-cover{height:160px}
    .posts-container.view-grid .post-content{padding:10px}
  }
  @media (max-width: 767.98px){
    .posts-container.view-grid{grid-template-columns:repeat(2, 1fr);gap:10px}
    .posts-container.view-grid .post-cover{height:140px}
    .posts-container.view-grid .post-content{padding:8px}
  }
  @media (max-width: 575.98px){
    .posts-container.view-grid{grid-template-columns:repeat(2, 1fr);gap:8px}
    .posts-container.view-grid .post-cover{height:120px}
    .posts-container.view-grid .post-content{padding:8px}
  }

  /* 卡片头部布局调整 */
  .card-hd{padding:16px 18px}
  @media (max-width: 767.98px){
    .card-hd{padding:12px 14px}
  }
  
  /* 移动端卡片内容优化 */
  @media (max-width: 767.98px){
    .card-bd{padding:10px 12px}
    .post-title{font-size:15px;margin-bottom:6px}
    .post-summary{font-size:12px;margin:4px 0;line-height:1.5}
    .post-meta{font-size:11px;gap:8px;margin-top:6px;flex-wrap:wrap}
    .post-meta-item i{font-size:12px}
    .post-tags{margin-top:4px;gap:3px}
    .post-tag{font-size:10px;padding:2px 6px}
  }
  
  /* 侧边栏移动端优化 */
  @media (max-width: 991.98px){
    .col-lg-3{margin-top:16px}
    .col-lg-3 .card{margin-bottom:12px}
    .col-lg-3 .card-hd{font-size:15px;padding:10px 12px}
    .col-lg-3 .card-bd{padding:10px 12px}
    .col-lg-3 .post-card{padding:8px 0}
    .col-lg-3 .post-title{font-size:13px;margin-bottom:4px}
    .col-lg-3 .muted{font-size:12px;margin:0 4px 4px 0}
  }
  
  /* 无限滚动加载样式 */
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  .infinite-scroll-loader{animation:fadeIn .3s ease}
  .infinite-scroll-end{animation:fadeIn .3s ease}
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  
  /* 主布局移动端优化 */
  @media (max-width: 767.98px){
    .layout-main .col-lg-9{margin-bottom:0}
    .layout-main .card{border-radius:10px;margin-bottom:12px}
    .main.container{padding-left:12px;padding-right:12px}
    .infinite-scroll-loader{padding:16px}
    .infinite-scroll-end{padding:16px}
  }
</style>
<script>
  (function(){
    var currentPage = 1;
    var isLoading = false;
    var hasMore = {{ $postsRaw->hasMorePages() ? 'true' : 'false' }};
    var tagSlug = '{{ $tagSlug ?? ($tag->slug ?? ("tags_".$tag->id)) }}';
    var currentView = 'list'; // 当前视图模式
    
    function initViewSwitcher(){
      var switcher = document.querySelector('.view-switcher');
      if (!switcher) {
        console.warn('View switcher not found');
        return;
      }
      
      var container = document.querySelector('.posts-container');
      if (!container) {
        console.warn('Posts container not found');
        return;
      }
      
      var btns = switcher.querySelectorAll('.view-btn');
      if (!btns.length) {
        console.warn('View buttons not found');
        return;
      }
      
      currentView = localStorage.getItem('tagViewMode') || 'list';
      
      function switchView(view){
        if (!view) return;
        container.className = 'posts-container view-' + view;
        currentView = view;
        btns.forEach(function(btn){
          var btnView = btn.getAttribute('data-view');
          if (btnView === view) {
            btn.classList.add('active');
          } else {
            btn.classList.remove('active');
          }
        });
        localStorage.setItem('tagViewMode', view);
      }
      
      // 初始化视图
      switchView(currentView);
      
      // 绑定切换事件
      btns.forEach(function(btn){
        btn.addEventListener('click', function(e){
          e.preventDefault();
          e.stopPropagation();
          var view = this.getAttribute('data-view');
          if (view) {
            switchView(view);
          }
        });
      });
    }
    
    // 渲染文章卡片HTML
    function renderPostHTML(post){
      var isGrid = currentView === 'grid';
      var html = '<div class="post-card">';
      
      var postUrl = post.slug ? '/' + post.slug : '#';
      if (post.cover) {
        html += '<div class="post-cover"><a href="' + postUrl + '"><img src="' + post.cover + '" alt="' + (post.title || '') + '"></a></div>';
      }
      
      html += '<div class="post-content">';
      html += '<div class="post-title"><a href="' + postUrl + '">' + (post.title || '') + '</a></div>';
      
      if (isGrid && post.summary) {
        html += '<div class="post-summary" data-view-hide="grid" style="display:none;">' + (post.summary || '') + '</div>';
      } else if (!isGrid && post.summary) {
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
      html += '<span class="post-meta-item"><i class="bi bi-calendar3"></i> ' + publishedDate + '</span>';
      html += '<span class="post-meta-item"><i class="bi bi-eye"></i> ' + (post.view_count || 0) + '</span>';
      html += '<span class="post-meta-item"><i class="bi bi-chat-dots"></i> ' + (post.comments_count || 0) + '</span>';
      html += '<span class="post-meta-item post-like-item" data-post-id="' + (post.id || '') + '" style="cursor:pointer;user-select:none;"><i class="bi bi-hand-thumbs-up"></i> <span class="likes-count">' + (post.likes_count || 0) + '</span></span>';
      html += '<span class="post-meta-item post-favorite-item" data-post-id="' + (post.id || '') + '" style="cursor:pointer;user-select:none;"><i class="bi bi-star"></i> <span class="favorites-count">' + (post.favorites_count || 0) + '</span></span>';
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
      var url = '{{ route("site.tag.loadMore", ["slug" => ":slug"]) }}'.replace(':slug', tagSlug) + '?page=' + currentPage;
      
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
            // 重新初始化点赞按钮（包括新加载的文章）
            initLikeButtons();
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
      
      var threshold = 200; // 距离底部200px时开始加载
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
    
    // 点赞功能
    function initLikeButtons(){
      var likeButtons = document.querySelectorAll('.post-like-item');
      if (!likeButtons.length) return;
      
      likeButtons.forEach(function(btn){
        // 避免重复绑定事件
        if (btn.dataset.likeBound === 'true') return;
        btn.dataset.likeBound = 'true';
        
        btn.addEventListener('click', function(e){
          e.preventDefault();
          e.stopPropagation();
          
          var postId = this.getAttribute('data-post-id');
          if (!postId) return;
          
          var icon = this.querySelector('i');
          var countSpan = this.querySelector('.likes-count');
          var originalClass = icon ? icon.className : '';
          
          // 禁用按钮，防止重复点击
          if (this.dataset.loading === 'true') return;
          this.dataset.loading = 'true';
          this.style.opacity = '0.6';
          this.style.pointerEvents = 'none';
          
          var url = '/posts/' + postId + '/like';
          var token = document.querySelector('meta[name="csrf-token"]')?.content || '';
          
          fetch(url, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': token,
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({})
          })
          .then(function(response){
            if (!response.ok) {
              return response.json().then(function(data) {
                throw new Error(data.message || '操作失败');
              });
            }
            return response.json();
          })
          .then(function(data){
            if (data.code === 0) {
              // 更新图标
              if (icon) {
                if (data.liked) {
                  icon.className = originalClass.replace('bi-hand-thumbs-up', 'bi-hand-thumbs-up-fill');
                } else {
                  icon.className = originalClass.replace('bi-hand-thumbs-up-fill', 'bi-hand-thumbs-up');
                }
              }
              // 更新数量
              if (countSpan) {
                countSpan.textContent = data.likes_count || 0;
              }
            } else {
              alert(data.message || '操作失败');
            }
          })
          .catch(function(error){
            console.error('Error:', error);
            alert(error.message || '操作失败，请重试');
          })
          .finally(function(){
            // 恢复按钮
            btn.dataset.loading = 'false';
            btn.style.opacity = '1';
            btn.style.pointerEvents = 'auto';
          });
        });
      });
    }
    
    // 收藏功能
    function initFavoriteButtons(){
      var favoriteButtons = document.querySelectorAll('.post-favorite-item');
      if (!favoriteButtons.length) return;
      
      favoriteButtons.forEach(function(btn){
        // 避免重复绑定事件
        if (btn.dataset.favoriteBound === 'true') return;
        btn.dataset.favoriteBound = 'true';
        
        btn.addEventListener('click', function(e){
          e.preventDefault();
          e.stopPropagation();
          
          var postId = this.getAttribute('data-post-id');
          if (!postId) return;
          
          var icon = this.querySelector('i');
          var countSpan = this.querySelector('.favorites-count');
          var originalClass = icon ? icon.className : '';
          
          // 禁用按钮，防止重复点击
          if (this.dataset.loading === 'true') return;
          this.dataset.loading = 'true';
          this.style.opacity = '0.6';
          this.style.pointerEvents = 'none';
          
          var url = '/posts/' + postId + '/favorite';
          var token = document.querySelector('meta[name="csrf-token"]')?.content || '';
          
          fetch(url, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': token,
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({})
          })
          .then(function(response){
            if (!response.ok) {
              return response.json().then(function(data) {
                throw new Error(data.message || '操作失败');
              });
            }
            return response.json();
          })
          .then(function(data){
            if (data.code === 0) {
              // 更新图标
              if (icon) {
                if (data.favorited) {
                  icon.className = originalClass.replace('bi-star', 'bi-star-fill');
                } else {
                  icon.className = originalClass.replace('bi-star-fill', 'bi-star');
                }
              }
              // 更新数量
              if (countSpan) {
                countSpan.textContent = data.favorites_count || 0;
              }
            } else {
              alert(data.message || '操作失败');
            }
          })
          .catch(function(error){
            console.error('Error:', error);
            alert(error.message || '操作失败，请重试');
          })
          .finally(function(){
            // 恢复按钮
            btn.dataset.loading = 'false';
            btn.style.opacity = '1';
            btn.style.pointerEvents = 'auto';
          });
        });
      });
    }
    
    // 初始化
    function init(){
      initViewSwitcher();
      initInfiniteScroll();
      initLikeButtons();
      initFavoriteButtons();
    }
    
    // 确保DOM加载完成后执行
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', init);
    } else {
      setTimeout(init, 50);
    }
  })();
</script>
@endsection

