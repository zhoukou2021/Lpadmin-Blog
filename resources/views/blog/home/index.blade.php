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
                    <span class="post-meta-item post-like-item" data-post-id="{{ $post['id'] }}" style="cursor:pointer;user-select:none;">
                      <i class="bi bi-hand-thumbs-up"></i> <span class="likes-count">{{ $post['likes_count'] ?? 0 }}</span>
                    </span>
                    <span class="post-meta-item post-favorite-item" data-post-id="{{ $post['id'] }}" style="cursor:pointer;user-select:none;">
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
              <div class="infinite-scroll-loader" data-type="new" style="display:none;padding:20px;text-align:center;border-top:1px solid #e6eee6;margin-top:20px;">
                <div class="loading-spinner" style="display:inline-block;width:32px;height:32px;border:3px solid #e6eee6;border-top-color:var(--primary);border-radius:50%;animation:spin .8s linear infinite;"></div>
                <div class="loading-text muted" style="margin-top:12px;font-size:14px;">加载中...</div>
              </div>
              <div class="infinite-scroll-end" data-type="new" style="display:none;padding:20px;text-align:center;border-top:1px solid #e6eee6;margin-top:20px;">
                <div class="muted" style="font-size:14px;">没有更多了</div>
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
                    <span class="post-meta-item post-like-item" data-post-id="{{ $post['id'] }}" style="cursor:pointer;user-select:none;">
                      <i class="bi bi-hand-thumbs-up"></i> <span class="likes-count">{{ $post['likes_count'] ?? 0 }}</span>
                    </span>
                    <span class="post-meta-item post-favorite-item" data-post-id="{{ $post['id'] }}" style="cursor:pointer;user-select:none;">
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
              <div class="infinite-scroll-loader" data-type="hot" style="display:none;padding:20px;text-align:center;border-top:1px solid #e6eee6;margin-top:20px;">
                <div class="loading-spinner" style="display:inline-block;width:32px;height:32px;border:3px solid #e6eee6;border-top-color:var(--primary);border-radius:50%;animation:spin .8s linear infinite;"></div>
                <div class="loading-text muted" style="margin-top:12px;font-size:14px;">加载中...</div>
              </div>
              <div class="infinite-scroll-end" data-type="hot" style="display:none;padding:20px;text-align:center;border-top:1px solid #e6eee6;margin-top:20px;">
                <div class="muted" style="font-size:14px;">没有更多了</div>
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
                    <span class="post-meta-item post-like-item" data-post-id="{{ $post['id'] }}" style="cursor:pointer;user-select:none;">
                      <i class="bi bi-hand-thumbs-up"></i> <span class="likes-count">{{ $post['likes_count'] ?? 0 }}</span>
                    </span>
                    <span class="post-meta-item post-favorite-item" data-post-id="{{ $post['id'] }}" style="cursor:pointer;user-select:none;">
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
              <div class="infinite-scroll-loader" data-type="rec" style="display:none;padding:20px;text-align:center;border-top:1px solid #e6eee6;margin-top:20px;">
                <div class="loading-spinner" style="display:inline-block;width:32px;height:32px;border:3px solid #e6eee6;border-top-color:var(--primary);border-radius:50%;animation:spin .8s linear infinite;"></div>
                <div class="loading-text muted" style="margin-top:12px;font-size:14px;">加载中...</div>
              </div>
              <div class="infinite-scroll-end" data-type="rec" style="display:none;padding:20px;text-align:center;border-top:1px solid #e6eee6;margin-top:20px;">
                <div class="muted" style="font-size:14px;">没有更多了</div>
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
<style>
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
</style>
  <script>
    // 全局函数：初始化点赞按钮
    function initLikeButtons(){
      var likeButtons = document.querySelectorAll('.post-like-item');
      if (!likeButtons.length) return;
      
      // 移除已绑定的事件监听器，避免重复绑定
      likeButtons.forEach(function(btn){
        var newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);
      });
      
      var currentButtons = document.querySelectorAll('.post-like-item');
      currentButtons.forEach(function(btn){
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
    
    // 全局函数：初始化收藏按钮
    function initFavoriteButtons(){
      var favoriteButtons = document.querySelectorAll('.post-favorite-item');
      if (!favoriteButtons.length) return;
      
      // 移除已绑定的事件监听器，避免重复绑定
      favoriteButtons.forEach(function(btn){
        var newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);
      });
      
      var currentButtons = document.querySelectorAll('.post-favorite-item');
      currentButtons.forEach(function(btn){
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
    
    // 初始化点赞和收藏按钮
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', function(){
        initLikeButtons();
        initFavoriteButtons();
      });
    } else {
      setTimeout(function(){
        initLikeButtons();
        initFavoriteButtons();
      }, 50);
    }
    
    // 无限滚动分页加载
    (function(){
      var currentType = 'new'; // 当前激活的标签类型
      var pageStates = {
        new: { page: 1, isLoading: false, hasMore: {{ count($latest ?? []) >= 10 ? 'true' : 'false' }} },
        hot: { page: 1, isLoading: false, hasMore: {{ count($hot ?? []) >= 10 ? 'true' : 'false' }} },
        rec: { page: 1, isLoading: false, hasMore: {{ count($recommended ?? []) >= 10 ? 'true' : 'false' }} }
      };
      
      // 渲染文章HTML
      function renderPostHTML(post){
        var html = '<div class="post-card">';
        if (post.cover) {
          html += '<div class="post-cover"><a href="' + (post.slug ? '/' + post.slug : '#') + '"><img src="' + (post.cover || '') + '" alt="' + (post.title || '') + '"></a></div>';
        }
        html += '<div class="post-content">';
        html += '<div class="post-title"><a href="' + (post.slug ? '/' + post.slug : '#') + '">' + (post.title || '') + '</a></div>';
        if (post.summary) {
          html += '<div class="post-summary">' + (post.summary || '') + '</div>';
        }
        html += '<div class="post-meta">';
        if (post.category_name) {
          html += '<a href="' + (post.category_slug ? '/' + post.category_slug : '#') + '" class="post-category"><i class="bi bi-folder"></i> ' + (post.category_name || '') + '</a>';
        }
        if (post.published_at) {
          var date = new Date(post.published_at);
          var dateStr = date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');
          html += '<span class="post-meta-item"><i class="bi bi-calendar3"></i> ' + dateStr + '</span>';
        }
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
      function loadMorePosts(type){
        var state = pageStates[type];
        if (!state || state.isLoading || !state.hasMore) return;
        
        state.isLoading = true;
        state.page++;
        
        var loader = document.querySelector('.infinite-scroll-loader[data-type="' + type + '"]');
        var end = document.querySelector('.infinite-scroll-end[data-type="' + type + '"]');
        if (loader) loader.style.display = 'block';
        if (end) end.style.display = 'none';
        
        var url = '{{ route("site.home.loadMore") }}?type=' + type + '&page=' + state.page;
        
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
            var container = document.querySelector('.posts-container[data-type="' + type + '"]');
            if (container) {
              data.data.forEach(function(post){
                container.insertAdjacentHTML('beforeend', renderPostHTML(post));
              });
              // 重新初始化点赞和收藏按钮
              initLikeButtons();
              initFavoriteButtons();
            }
            state.hasMore = data.has_more || false;
            if (!state.hasMore && end) {
              end.style.display = 'block';
            }
          } else {
            state.hasMore = false;
            if (end) end.style.display = 'block';
          }
        })
        .catch(function(error){
          console.error('Error loading more posts:', error);
          state.hasMore = false;
          if (end) end.style.display = 'block';
        })
        .finally(function(){
          state.isLoading = false;
          if (loader) loader.style.display = 'none';
        });
      }
      
      // 无限滚动监听
      function initInfiniteScroll(){
        var threshold = 200;
        var ticking = false;
        
        function checkScroll(){
          var state = pageStates[currentType];
          if (!state || !state.hasMore || state.isLoading) {
            ticking = false;
            return;
          }
          
          var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
          var windowHeight = window.innerHeight || document.documentElement.clientHeight;
          var documentHeight = document.documentElement.scrollHeight;
          
          if (scrollTop + windowHeight >= documentHeight - threshold) {
            loadMorePosts(currentType);
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
      
      // Tab切换
      function initTabs(){
        var tabs = document.querySelectorAll('.tabs a');
        var panes = {
          rec: document.getElementById('tab-rec'),
          new: document.getElementById('tab-new'),
          hot: document.getElementById('tab-hot')
        };
        if (!tabs.length) return;
        tabs.forEach(function(a) {
          a.addEventListener('click', function(e) {
            e.preventDefault();
            var tabName = this.getAttribute('data-tab');
            if (!tabName || !panes[tabName]) return;
            
            // 更新当前类型
            currentType = tabName;
            
            tabs.forEach(function(x) { x.classList.remove('active'); });
            this.classList.add('active');
            Object.keys(panes).forEach(function(key) {
              if (panes[key]) panes[key].style.display = 'none';
            });
            if (panes[tabName]) {
              panes[tabName].style.display = 'block';
              panes[tabName].classList.remove('fade-in');
              void panes[tabName].offsetWidth;
              panes[tabName].classList.add('fade-in');
            }
          });
        });
      }
      
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function(){
          initTabs();
          initInfiniteScroll();
        });
      } else {
        setTimeout(function(){
          initTabs();
          initInfiniteScroll();
        }, 100);
    }
  })();
  </script>
@endsection


