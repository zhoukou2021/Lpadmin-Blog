/**
 * 会员中心文章列表页面功能（收藏、点赞）
 */
(function() {
  'use strict';
  
  // 渲染文章卡片HTML
  function renderPostHTML(post) {
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
    
    // 根据页面类型使用不同的图标
    var likeIcon = 'bi-hand-thumbs-up';
    var favoriteIcon = 'bi-star';
    if (window.MemberPostsConfig && window.MemberPostsConfig.type === 'likes') {
      likeIcon = 'bi-hand-thumbs-up-fill';
      favoriteIcon = 'bi-star';
    } else if (window.MemberPostsConfig && window.MemberPostsConfig.type === 'favorites') {
      likeIcon = 'bi-hand-thumbs-up';
      favoriteIcon = 'bi-star-fill';
    }
    
    html += '<div class="post-meta-item post-like-item" data-post-id="' + (post.id || '') + '" style="cursor:pointer;user-select:none;"><i class="bi ' + likeIcon + '"></i> <span class="likes-count">' + (post.likes_count || 0) + '</span></div>';
    html += '<div class="post-meta-item post-favorite-item" data-post-id="' + (post.id || '') + '" style="cursor:pointer;user-select:none;"><i class="bi ' + favoriteIcon + '"></i> <span class="favorites-count">' + (post.favorites_count || 0) + '</span></div>';
    html += '</div>';
    
    if (post.tags && post.tags.length > 0) {
      html += '<div class="post-tags">';
      post.tags.forEach(function(tag) {
        var tagUrl = tag.slug ? '/' + tag.slug : '#';
        html += '<a href="' + tagUrl + '" class="post-tag">#' + (tag.name || '') + '</a>';
      });
      html += '</div>';
    }
    
    html += '</div></div>';
    return html;
  }
  
  // 初始化文章列表页面无限滚动
  function initPostsPage() {
    if (typeof window.MemberInfiniteScroll === 'undefined') {
      setTimeout(initPostsPage, 50);
      return;
    }
    
    var config = window.MemberPostsConfig || {};
    if (!config.loadMoreUrl || !config.hasMore) return;
    
    window.MemberInfiniteScroll.init({
      loadMoreUrl: config.loadMoreUrl,
      hasMore: config.hasMore,
      renderItem: renderPostHTML,
      containerSelector: '.posts-container',
      loaderId: 'infiniteScrollLoader',
      endId: 'infiniteScrollEnd'
    });
  }
  
  // 初始化
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPostsPage);
  } else {
    initPostsPage();
  }
})();

