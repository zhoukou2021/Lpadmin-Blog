/**
 * 会员中心评论页面功能
 */
(function() {
  'use strict';
  
  // 渲染评论HTML
  function renderCommentHTML(comment, userNickname) {
    userNickname = userNickname || '';
    var html = '<div class="comment-item">';
    html += '<div class="comment-content">';
    html += '<div class="comment-header">';
    html += '<span class="comment-author">' + userNickname + '</span>';
    html += '<span class="comment-time">' + (comment.created_at || '') + '</span>';
    html += '</div>';
    html += '<div class="comment-text">' + (comment.content || '') + '</div>';
    
    if (comment.post && comment.post.title) {
      var postUrl = comment.post.slug ? '/' + comment.post.slug : '#';
      html += '<div class="comment-post-link">';
      html += '<i class="bi bi-file-text"></i>';
      html += '<a href="' + postUrl + '">' + comment.post.title + '</a>';
      html += '</div>';
    }
    
    if (comment.status === 'pending') {
      html += '<div class="comment-status">';
      html += '<span class="comment-status-pending">' + (window.MemberConfig && window.MemberConfig.texts && window.MemberConfig.texts.commentPending || '待审核') + '</span>';
      html += '</div>';
    } else if (comment.status === 'rejected') {
      html += '<div class="comment-status">';
      html += '<span class="comment-status-rejected">' + (window.MemberConfig && window.MemberConfig.texts && window.MemberConfig.texts.commentRejected || '已拒绝') + '</span>';
      html += '</div>';
    }
    
    html += '</div></div>';
    return html;
  }
  
  // 初始化评论页面无限滚动
  function initCommentsPage() {
    if (typeof window.MemberInfiniteScroll === 'undefined') {
      setTimeout(initCommentsPage, 50);
      return;
    }
    
    var config = window.MemberCommentsConfig || {};
    if (!config.loadMoreUrl || !config.hasMore) return;
    
    window.MemberInfiniteScroll.init({
      loadMoreUrl: config.loadMoreUrl,
      hasMore: config.hasMore,
      renderItem: function(comment) {
        return renderCommentHTML(comment, config.userNickname);
      },
      containerSelector: '.comments-list',
      loaderId: 'infiniteScrollLoader',
      endId: 'infiniteScrollEnd'
    });
  }
  
  // 初始化
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCommentsPage);
  } else {
    initCommentsPage();
  }
})();

