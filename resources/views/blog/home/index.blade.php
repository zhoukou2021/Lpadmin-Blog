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
          <div class="tab-pane" id="tab-hot" style="display:none">
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
          <div class="tab-pane" id="tab-rec" style="display:none">
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
        </div>
      </div>
    </div>

    @include('blog.components.sidebar')
  </div>
@endsection

@section('head')
  @include('blog.components.banner-3d-styles')
  @include('blog.components.banner-3d-script', ['banners' => $banners ?? [], 'bannerId' => 'banner'])
  <script>
    // 点赞功能
    (function(){
      function initLikeButtons(){
        var likeButtons = document.querySelectorAll('.post-like-item');
        if (!likeButtons.length) return;
        
        likeButtons.forEach(function(btn){
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
      
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLikeButtons);
      } else {
        setTimeout(initLikeButtons, 50);
      }
    })();
    
    // Tab切换
    (function(){
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
        document.addEventListener('DOMContentLoaded', initTabs);
      } else {
      setTimeout(initTabs, 100);
    }
  })();
  
  // 点赞功能
  (function(){
    function initLikeButtons(){
      var likeButtons = document.querySelectorAll('.post-like-item');
      if (!likeButtons.length) return;
      
      likeButtons.forEach(function(btn){
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
    
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initLikeButtons);
    } else {
      setTimeout(initLikeButtons, 50);
    }
  })();
  
  // 收藏功能
  (function(){
    function initFavoriteButtons(){
      var favoriteButtons = document.querySelectorAll('.post-favorite-item');
      if (!favoriteButtons.length) return;
      
      favoriteButtons.forEach(function(btn){
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
    
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initFavoriteButtons);
    } else {
      setTimeout(initFavoriteButtons, 50);
    }
  })();
  </script>
@endsection


