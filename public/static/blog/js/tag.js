/**
 * 标签页面 JavaScript
 * 处理视图切换、无限滚动、点赞、收藏等功能
 */

(function(){
    'use strict';
    
    if (typeof window.BlogTagConfig === 'undefined') {
        console.error('BlogTagConfig is not defined');
        return;
    }
    
    var config = window.BlogTagConfig;
    var currentPage = 1;
    var isLoading = false;
    var hasMore = config.hasMore || false;
    var tagSlug = config.tagSlug || '';
    var currentView = 'list'; // 当前视图模式
    
    // 视图切换功能
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
        html += '<div class="post-summary" data-view-hide="grid">' + (post.summary || '') + '</div>';
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
      html += '<span class="post-meta-item post-like-item" data-post-id="' + (post.id || '') + '"><i class="bi bi-hand-thumbs-up"></i> <span class="likes-count">' + (post.likes_count || 0) + '</span></span>';
      html += '<span class="post-meta-item post-favorite-item" data-post-id="' + (post.id || '') + '"><i class="bi bi-star"></i> <span class="favorites-count">' + (post.favorites_count || 0) + '</span></span>';
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
      var url = config.loadMoreUrl.replace(':slug', tagSlug) + '?page=' + currentPage;
      
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
            // 重新初始化点赞和收藏按钮（包括新加载的文章）
            initLikeButtons();
            initFavoriteButtons();
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
    
    // 暴露函数到全局作用域，供动态加载的内容使用
    window.initLikeButtons = initLikeButtons;
    window.initFavoriteButtons = initFavoriteButtons;
})();

